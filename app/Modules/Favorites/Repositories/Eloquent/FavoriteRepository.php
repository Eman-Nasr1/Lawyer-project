<?php
namespace App\Modules\Favorites\Repositories\Eloquent;

use App\Modules\Favorites\Repositories\FavoriteRepositoryInterface;
use App\Models\Favorite;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FavoriteRepository implements FavoriteRepositoryInterface
{
    public function exists(int $userId, string $type, int $id): bool
    {
        return Favorite::where([
            'user_id'     => $userId,   // ✅ لا تستخدم compact('user_id') هنا
            'favoritable_type' => $type,     // 'lawyer' | 'company'
            'favoritable_id'   => $id,
        ])->exists();
    }

    public function add(int $userId, string $type, int $id): void
    {
        Favorite::firstOrCreate([
            'user_id' => $userId,
            'favoritable_type' => $type,
            'favoritable_id' => $id,
        ]);
    }

    public function remove(int $userId, string $type, int $id): void
    {
        Favorite::where('user_id',$userId)
            ->where('favoritable_type',$type)
            ->where('favoritable_id',$id)
            ->delete();
    }

    public function listForUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        // ترجيع Paginator مطابق للـInterface
        return Favorite::where('user_id', $userId)
            ->orderByDesc('id')
            ->paginate($perPage);
    }
}
