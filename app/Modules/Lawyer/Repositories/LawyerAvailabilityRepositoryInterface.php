<?php
namespace App\Modules\Lawyer\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\LawyerAvailability;

interface LawyerAvailabilityRepositoryInterface {
    public function paginateForLawyer(int $lawyerId, int $perPage = 15): LengthAwarePaginator;
    public function create(array $data): LawyerAvailability;
    public function update(int $id, array $data): LawyerAvailability;
    public function delete(int $id): void;
    public function getForDate(int $lawyerId, string $date);
    public function find(int $id): LawyerAvailability;
}
