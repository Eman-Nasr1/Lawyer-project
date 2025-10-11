<?php

namespace App\Modules\Specialties\Services;

use App\Models\Specialty;
use App\Modules\Specialties\Repositories\SpecialtyRepositoryInterface as Repo;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use InvalidArgumentException;

class SpecialtyService
{
    public function __construct(private Repo $repo) {}

    public function list(int $perPage = 12, ?string $search = null): LengthAwarePaginator
    {
        return $this->repo->paginate($perPage, $search);
    }

    public function get(int $id): Specialty
    {
        return $this->repo->findOrFail($id);
    }

    public function create(array $data): Specialty
    {
        $data['slug'] ??= Str::slug($data['name']);
        if ($this->repo->existsBySlug($data['slug'])) {
            throw new InvalidArgumentException('Slug already exists');
        }
        return $this->repo->create($data);
    }

    public function update(int $id, array $data): Specialty
    {
        if (!empty($data['name']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        if (!empty($data['slug']) && $this->repo->existsBySlug($data['slug'], $id)) {
            throw new InvalidArgumentException('Slug already exists');
        }
        return $this->repo->update($id, $data);
    }

    public function delete(int $id): void
    {
        $this->repo->delete($id);
    }
}
