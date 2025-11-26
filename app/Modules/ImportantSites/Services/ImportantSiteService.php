<?php

namespace App\Modules\ImportantSites\Services;

use App\Models\ImportantSite;
use App\Modules\ImportantSites\Repositories\ImportantSiteRepositoryInterface as Repo;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ImportantSiteService
{
    public function __construct(private Repo $repo) {}

    public function list(int $perPage = 12, ?string $search = null): LengthAwarePaginator
    {
        return $this->repo->paginate($perPage, $search);
    }

    public function get(int $id): ImportantSite
    {
        return $this->repo->findOrFail($id);
    }

    public function create(array $data): ImportantSite
    {
        return $this->repo->create($data);
    }

    public function update(int $id, array $data): ImportantSite
    {
        return $this->repo->update($id, $data);
    }

    public function delete(int $id): void
    {
        $this->repo->delete($id);
    }
}

