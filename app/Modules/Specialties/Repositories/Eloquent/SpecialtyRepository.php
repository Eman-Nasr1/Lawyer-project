<?php

namespace App\Modules\Specialties\Repositories\Eloquent;

use App\Models\Specialty;
use App\Modules\Specialties\Repositories\SpecialtyRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SpecialtyRepository implements SpecialtyRepositoryInterface
{
    public function paginate(int $perPage = 12, ?string $search = null): LengthAwarePaginator
    {
        $q = Specialty::query()->latest();
        if ($search) {
            $term = "%{$search}%";
            $q->where(fn($w)=>$w->where('name','like',$term)->orWhere('slug','like',$term));
        }
        return $q->paginate($perPage);
    }

    public function findOrFail(int $id): Specialty
    {
        return Specialty::findOrFail($id);
    }

    public function create(array $data): Specialty
    {
        return Specialty::create($data);
    }

    public function update(int $id, array $data): Specialty
    {
        $m = $this->findOrFail($id);
        $m->update($data);
        return $m;
    }

    public function delete(int $id): void
    {
        $this->findOrFail($id)->delete();
    }

    public function existsBySlug(string $slug, ?int $ignoreId = null): bool
    {
        $q = Specialty::where('slug',$slug);
        if ($ignoreId) $q->where('id','<>',$ignoreId);
        return $q->exists();
    }
}
