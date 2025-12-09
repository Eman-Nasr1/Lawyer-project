<?php
namespace App\Modules\Company\Repositories\Eloquent;

use App\Modules\Company\Repositories\CompanyAvailabilityRepositoryInterface;
use App\Models\CompanyAvailability;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CompanyAvailabilityRepository implements CompanyAvailabilityRepositoryInterface
{
    public function paginateForCompany(int $companyId, int $perPage = 15): LengthAwarePaginator {
        return CompanyAvailability::where('company_id', $companyId)->latest()->paginate($perPage);
    }
    public function create(array $data): CompanyAvailability { return CompanyAvailability::create($data); }
    public function update(int $id, array $data): CompanyAvailability {
        $m = CompanyAvailability::findOrFail($id); $m->update($data); return $m;
    }
    public function delete(int $id): void { CompanyAvailability::findOrFail($id)->delete(); }
    public function getForDate(int $companyId, string $date) {
        return CompanyAvailability::active()->where('company_id', $companyId)->forDate($date)->get();
    }
    public function find(int $id): CompanyAvailability { return CompanyAvailability::findOrFail($id); }
}

