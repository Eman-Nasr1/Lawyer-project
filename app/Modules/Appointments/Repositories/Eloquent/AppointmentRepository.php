<?php

namespace App\Modules\Appointments\Repositories\Eloquent;

use App\Modules\Appointments\Repositories\AppointmentRepositoryInterface;
use App\Models\Appointment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Models\AppointmentFile;
use App\Models\AppointmentCancellation;

class AppointmentRepository implements AppointmentRepositoryInterface
{
    public function paginateForClient(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Appointment::where('user_id', $userId)->latest()->paginate($perPage);
    }

    public function paginateForLawyer(int $lawyerId, int $perPage = 15): LengthAwarePaginator
    {
        return Appointment::where('lawyer_id', $lawyerId)->latest()->paginate($perPage);
    }

    public function create(array $data): Appointment
    {
        return Appointment::create($data);
    }

    public function updateStatus(int $id, string $status): Appointment
    {
        $a = Appointment::findOrFail($id);
        $a->update(['status' => $status]);
        return $a;
    }

    public function delete(int $id): void
    {
        Appointment::findOrFail($id)->delete();
    }

    public function forLawyerOnDate(int $lawyerId, string $date): Collection
    {
        return Appointment::where('lawyer_id', $lawyerId)
            ->whereDate('date', $date)
            ->whereIn('status', ['pending','confirmed'])
            ->get();
    }

    public function find(int $id): Appointment
    {
        return Appointment::findOrFail($id);
    }
    public function addFiles(int $appointmentId, array $filesData): void
{
    foreach ($filesData as $row) {
        AppointmentFile::create($row);
    }
}

public function listFiles(int $appointmentId)
{
    return AppointmentFile::where('appointment_id', $appointmentId)->latest()->get();
}

public function logCancellation(int $appointmentId, int $userId, ?string $reason): void
{
    AppointmentCancellation::create([
        'appointment_id' => $appointmentId,
        'cancelled_by'   => $userId,
        'reason'         => $reason,
        'cancelled_at'   => now(),
    ]);
}
}
