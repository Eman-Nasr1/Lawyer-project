<?php

namespace App\Modules\LegalDecisions\Repositories;

use App\Models\LegalDecisionCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CategoryRepositoryInterface
{
    public function paginate(int $perPage = 12, ?string $search = null): LengthAwarePaginator;

    /** في حالات القوائم (selects) */
    public function allForSelect(): Collection;

    public function find(int $id): ?LegalDecisionCategory;

    public function findOrFail(int $id): LegalDecisionCategory;

    public function create(array $data): LegalDecisionCategory;

    public function update(LegalDecisionCategory $category, array $data): void;

    public function delete(LegalDecisionCategory $category): void;
}
