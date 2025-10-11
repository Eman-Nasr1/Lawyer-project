<?php

namespace App\Modules\LegalDecisions\Repositories;

use App\Models\LegalDecision;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface DecisionRepositoryInterface
{
    public function paginate(
        int $perPage = 12,
        ?string $search = null,
        ?int $categoryId = null
    ): LengthAwarePaginator;

    public function find(int $id): ?LegalDecision;

    public function findOrFail(int $id): LegalDecision;

    public function create(array $data): LegalDecision;

    public function update(LegalDecision $decision, array $data): void;

    public function delete(LegalDecision $decision): void;
}
