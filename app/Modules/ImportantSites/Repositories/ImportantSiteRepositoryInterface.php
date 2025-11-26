<?php

namespace App\Modules\ImportantSites\Repositories;

use App\Models\ImportantSite;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ImportantSiteRepositoryInterface
{
    public function paginate(int $perPage = 12, ?string $search = null): LengthAwarePaginator;
    public function findOrFail(int $id): ImportantSite;
    public function create(array $data): ImportantSite;
    public function update(int $id, array $data): ImportantSite;
    public function delete(int $id): void;
}

