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
    
        $lawyerId  = $data['lawyer_id']  ?? null;
        $companyId = $data['company_id'] ?? null;
    
        if (!$lawyerId && !$companyId) {
            throw new RuntimeException('You must select a lawyer or a company.');
        }
    
        // لو فيه محامي → نطبق الـ availability + overlap
        if ($lawyerId) {
            if (!$this->isWithinAvailability($lawyerId, $data['date'], $data['start_time'], $data['end_time'])) {
                throw new RuntimeException('Selected time is outside lawyer availability.');
            }
    
            $this->assertNoOverlapWithAppointments(
                $lawyerId,
                $data['date'],
                $data['start_time'],
                $data['end_time']
            );
        }
    
        // مهم عشان نضمن إن المفاتيح موجودة في الـ array
        $data['lawyer_id']  = $lawyerId;   // هتبقى null لو مفيش محامي
        $data['company_id'] = $companyId;  // أو id لو موجود
    
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

        return response()->json([
            'status' => 'success',
            'message' => 'تم الإلغاء بنجاح'
        ], 200);
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
    public function assignToLawyerByCompany(int $appointmentId, int $lawyerId, int $companyId)
    {
        // 1) نجيب الـ appointment ونتأكد إنه تابع للشركة
        $appointment = $this->repo->findForCompany($appointmentId, $companyId);
    
        if (!$appointment) {
            throw new \RuntimeException('Appointment does not belong to this company.');
        }
    
        // 2) نتأكد إن المحامي تابع لنفس الشركة (من خلال الـ pivot company_lawyer)
        $lawyer = \App\Models\Lawyer::where('id', $lawyerId)
            ->whereHas('companies', function ($q) use ($companyId) {
                $q->where('companies.id', $companyId);
            })
            ->first();
    
        if (!$lawyer) {
            throw new \RuntimeException('Lawyer does not belong to this company.');
        }
    
        // 3) نسند المحامي للميعاد
        $appointment->lawyer_id = $lawyer->id;
        $appointment->save();
    
        return $appointment->fresh();
    }
    public function updateStatusByCompany(int $appointmentId, string $status, int $companyId)
{
    // نتأكد أن الموعد تابع للشركة
    $appointment = $this->repo->findForCompany($appointmentId, $companyId);

    if (!$appointment) {
        throw new RuntimeException('Appointment does not belong to this company.');
    }

    // ممكن تمنعي تعديل completed/cancelled
    if (in_array($appointment->status, ['completed', 'cancelled'])) {
        throw new RuntimeException('This appointment cannot be updated.');
    }

    // تعديل الحالة
    $appointment->status = $status;
    $appointment->save();

    return $appointment->fresh();
}

    

}
