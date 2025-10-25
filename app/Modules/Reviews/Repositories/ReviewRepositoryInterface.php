<?php
namespace App\Modules\Reviews\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Review;

interface ReviewRepositoryInterface
{
    public function create(array $data): Review;
    public function forLawyer(int $lawyerId, int $perPage = 15): LengthAwarePaginator;
    public function forTarget(string $reviewableType, int $reviewableId, int $perPage = 15): LengthAwarePaginator;
    public function averageForLawyer(int $lawyerId): array; // ['avg'=>..,'count'=>..]
    public function averageFor(string $reviewableType, int $reviewableId): array; // ['avg'=>..,'count'=>..]
    public function existsForAppointment(int $appointmentId, int $reviewerId): bool;
}
