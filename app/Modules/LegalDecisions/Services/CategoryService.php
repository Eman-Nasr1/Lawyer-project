<?php

namespace App\Modules\LegalDecisions\Services;

use App\Models\LegalDecisionCategory;

class CategoryService
{
    public function paginate(int $perPage = 12, ?string $search = null)
    {
        return LegalDecisionCategory::query()
            ->when($search, fn($q)=>$q->where('name','like',"%$search%")->orWhere('slug','like',"%$search%"))
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function create(array $data): LegalDecisionCategory
    {
        return LegalDecisionCategory::create($data);
    }

    public function update(LegalDecisionCategory $category, array $data): void
    {
        $category->update($data);
    }

    public function delete(LegalDecisionCategory $category): void
    {
        $category->delete();
    }
}
