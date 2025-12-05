<?php
namespace App\Modules\Favorites\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface FavoriteRepositoryInterface
{
    public function exists(int $userId, string $type, int $id): bool;
    public function add(int $userId, string $type, int $id): void;
    public function remove(int $userId, string $type, int $id): void;
    public function listForUser(int $userId, int $perPage = 15, ?string $favoritableType = null): LengthAwarePaginator;
}
