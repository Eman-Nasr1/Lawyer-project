<?php

namespace App\Modules\LegalDecisions\Services;

use App\Models\LegalDecision;

class DecisionService
{
    public function paginate(int $perPage = 12, ?string $search = null, ?int $categoryId = null)
    {
        return LegalDecision::query()
            ->with('category:id,name')
            ->when($categoryId, fn($q)=>$q->where('category_id',$categoryId))
            ->when($search, fn($q)=>$q->where('title','like',"%$search%"))
            ->orderByDesc('published_at')
            ->latest('id')
            ->paginate($perPage);
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
