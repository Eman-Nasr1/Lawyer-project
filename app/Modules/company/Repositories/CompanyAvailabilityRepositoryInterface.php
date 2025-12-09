<?php
namespace App\Modules\Company\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\CompanyAvailability;

interface CompanyAvailabilityRepositoryInterface {
    public function paginateForCompany(int $companyId, int $perPage = 15): LengthAwarePaginator;
    public function create(array $data): CompanyAvailability;
    public function update(int $id, array $data): CompanyAvailability;
    public function delete(int $id): void;
    public function getForDate(int $companyId, string $date);
    public function find(int $id): CompanyAvailability;
}

