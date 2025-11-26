<?php

namespace App\Modules\ImportantSites\Repositories\Eloquent;

use App\Models\ImportantSite;
use App\Modules\ImportantSites\Repositories\ImportantSiteRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ImportantSiteRepository implements ImportantSiteRepositoryInterface
{
    public function paginate(int $perPage = 12, ?string $search = null): LengthAwarePaginator
    {
        $q = ImportantSite::query()->orderBy('sort_order')->orderBy('id', 'desc');
        if ($search) {
            $term = "%{$search}%";
            $q->where(fn($w) => $w->where('name', 'like', $term)
                ->orWhere('url', 'like', $term)
                ->orWhere('description', 'like', $term));
        }
        return $q->paginate($perPage);
    }

    public function findOrFail(int $id): ImportantSite
    {
        return ImportantSite::findOrFail($id);
    }

    public function create(array $data): ImportantSite
    {
        return ImportantSite::create($data);
    }

    public function update(int $id, array $data): ImportantSite
    {
        $m = $this->findOrFail($id);
        $m->update($data);
        return $m;
    }

    public function delete(int $id): void
    {
        $this->findOrFail($id)->delete();
    }
}

