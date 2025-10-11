<?php

namespace App\Modules\Specialties\Repositories;

use App\Models\Specialty;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SpecialtyRepositoryInterface
{
    public function paginate(int $perPage = 12, ?string $search = null): LengthAwarePaginator;
    public function findOrFail(int $id): Specialty;
    public function create(array $data): Specialty;
    public function update(int $id, array $data): Specialty;
    public function delete(int $id): void;
    public function existsBySlug(string $slug, ?int $ignoreId = null): bool;
}
