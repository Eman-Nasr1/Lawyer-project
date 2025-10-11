<?php

namespace App\Modules\LegalDecisions\Repositories\Eloquent;

use App\Models\LegalDecision;
use App\Modules\LegalDecisions\Repositories\DecisionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DecisionRepository implements DecisionRepositoryInterface
{
    public function paginate(
        int $perPage = 12,
        ?string $search = null,
        ?int $categoryId = null
    ): LengthAwarePaginator {
        return LegalDecision::query()
            ->with('category:id,name')
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->when($search, fn($q) => $q->where('title', 'like', "%{$search}%"))
            ->orderByDesc('published_at')
            ->latest('id')
            ->paginate($perPage);
    }

    public function find(int $id): ?LegalDecision
    {
        return LegalDecision::find($id);
    }

    public function findOrFail(int $id): LegalDecision
    {
        return LegalDecision::findOrFail($id);
    }

    public function create(array $data): LegalDecision
    {
        return LegalDecision::create($data);
    }

    public function update(LegalDecision $decision, array $data): void
    {
        $decision->update($data);
    }

    public function delete(LegalDecision $decision): void
    {
        $decision->delete();
    }
}
