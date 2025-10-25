<?php

namespace App\Modules\Appointments\Services;

use App\Modules\Appointments\Repositories\AppointmentRepositoryInterface;
use App\Models\LawyerAvailability;
use Illuminate\Http\UploadedFile;
use RuntimeException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AppointmentService
{
    public function __construct(private AppointmentRepositoryInterface $repo) {}

    public function createForClient(array $data, int $userId): \App\Models\Appointment
    {
        $data['user_id'] = $userId;
        $data['status']  = 'pending';

        if (!$this->isWithinAvailability($data['lawyer_id'], $data['date'], $data['start_time'], $data['end_time'])) {
            throw new RuntimeException('Selected time is outside lawyer availability.');
        }
        $this->assertNoOverlapWithAppointments($data['lawyer_id'], $data['date'], $data['start_time'], $data['end_time']);

        return $this->repo->create($data);
    }

    public function attachFiles(int $appointmentId, int $uploaderId, array $files, ?array $names = null)
    {
        $rows = [];
        foreach ($files as $idx => $file) {
            /** @var UploadedFile $file */
            $original = $names[$idx] ?? $file->getClientOriginalName();
            $safe     = Str::slug(pathinfo($original, PATHINFO_FILENAME));
            $ext      = $file->getClientOriginalExtension();
            $stored   = $file->storeAs("appointments/{$appointmentId}", "{$safe}-".Str::random(6).".{$ext}", 'public');

            $rows[] = [
                'appointment_id' => $appointmentId,
                'uploader_id'    => $uploaderId,
                'name'           => $original,
                'path'           => $stored,
            ];
        }
        $this->repo->addFiles($appointmentId, $rows);
        return $this->repo->listFiles($appointmentId);
    }

    public function cancel(int $appointmentId, int $byUserId, ?string $reason = null)
    {
        $a = $this->repo->find($appointmentId);

        // لو بالفعل ملغي/مكتمل، امنع تكرار
        if (in_array($a->status, ['cancelled','completed'])) {
            throw new RuntimeException('Appointment cannot be cancelled.');
        }

        $this->repo->updateStatus($appointmentId, 'cancelled');
        $this->repo->logCancellation($appointmentId, $byUserId, $reason);

        return $this->repo->find($appointmentId);
    }

    public function updateStatusByLawyer(int $appointmentId, string $status, int $lawyerId): \App\Models\Appointment
    {
        $a = $this->repo->find($appointmentId);
        if ((int)$a->lawyer_id !== (int)$lawyerId) {
            throw new RuntimeException('You cannot modify another lawyer\'s appointment.');
        }
        return $this->repo->updateStatus($appointmentId, $status);
    }

    // ===== helpers =====
    private function isWithinAvailability(int $lawyerId, string $date, string $start, string $end): bool
    {
        $dow = strtolower(date('l', strtotime($date)));
        $avail = LawyerAvailability::query()
            ->where('lawyer_id', $lawyerId)
            ->where('is_active', true)
            ->where(function ($q) use ($date, $dow) {
                $q->where('date', $date)->orWhere('day_of_week', $dow);
            })
            ->get();

        foreach ($avail as $a) if ($this->within($start, $end, $a->start_time, $a->end_time)) return true;
        return false;
    }
    private function within(string $s, string $e, string $as, string $ae): bool { return ($s >= $as && $e <= $ae); }
    private function assertNoOverlapWithAppointments(int $lawyerId, string $date, string $s, string $e): void
    {
        $booked = $this->repo->forLawyerOnDate($lawyerId, $date);
        foreach ($booked as $b) if (!($e <= $b->start_time || $s >= $b->end_time))
            throw new RuntimeException('Selected time overlaps with another appointment.');
    }
}
