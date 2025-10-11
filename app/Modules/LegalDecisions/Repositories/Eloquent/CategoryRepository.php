<?php

namespace App\Modules\LegalDecisions\Repositories\Eloquent;

use App\Models\LegalDecisionCategory as Category;
use App\Modules\LegalDecisions\Repositories\CategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function paginate(int $perPage = 12, ?string $search = null): LengthAwarePaginator
    {
        return Category::query()
            ->when($search, fn($q) =>
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
            )
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function allForSelect(): Collection
    {
        return Category::orderBy('name')->get(['id','name']);
    }

    public function find(int $id): ?Category
    {
        return Category::find($id);
    }

    public function findOrFail(int $id): Category
    {
        return Category::findOrFail($id);
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(Category $category, array $data): void
    {
        $category->update($data);
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }
}
