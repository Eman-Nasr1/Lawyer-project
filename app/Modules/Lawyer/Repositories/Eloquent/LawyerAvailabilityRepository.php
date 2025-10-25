<?php
namespace App\Modules\Lawyer\Repositories\Eloquent;

use App\Modules\Lawyer\Repositories\LawyerAvailabilityRepositoryInterface;
use App\Models\LawyerAvailability;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LawyerAvailabilityRepository implements LawyerAvailabilityRepositoryInterface
{
    public function paginateForLawyer(int $lawyerId, int $perPage = 15): LengthAwarePaginator {
        return LawyerAvailability::where('lawyer_id', $lawyerId)->latest()->paginate($perPage);
    }
    public function create(array $data): LawyerAvailability { return LawyerAvailability::create($data); }
    public function update(int $id, array $data): LawyerAvailability {
        $m = LawyerAvailability::findOrFail($id); $m->update($data); return $m;
    }
    public function delete(int $id): void { LawyerAvailability::findOrFail($id)->delete(); }
    public function getForDate(int $lawyerId, string $date) {
        return LawyerAvailability::active()->where('lawyer_id', $lawyerId)->forDate($date)->get();
    }
    public function find(int $id): LawyerAvailability { return LawyerAvailability::findOrFail($id); }
}
