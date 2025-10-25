<?php

namespace App\Modules\Appointments\Repositories;

use App\Models\Appointment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface AppointmentRepositoryInterface
{
    public function paginateForClient(int $userId, int $perPage = 15): LengthAwarePaginator;
    public function paginateForLawyer(int $lawyerId, int $perPage = 15): LengthAwarePaginator;

    public function create(array $data): Appointment;
    public function updateStatus(int $id, string $status): Appointment;
    public function delete(int $id): void;

    /** حجوزات يوم محدد للمحامي (لمنع التداخل) */
    public function forLawyerOnDate(int $lawyerId, string $date): Collection;
    public function find(int $id): Appointment;
    public function addFiles(int $appointmentId, array $filesData): void;
public function listFiles(int $appointmentId);
public function logCancellation(int $appointmentId, int $userId, ?string $reason): void;

}
