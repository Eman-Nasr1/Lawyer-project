<?php
namespace App\Modules\Favorites\Repositories\Eloquent;

use App\Modules\Favorites\Repositories\FavoriteRepositoryInterface;
use App\Models\Favorite;
use App\Models\Lawyer;
use App\Models\Company;
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
        Favorite::where('user_id', $userId)
            ->where('favoritable_type', $type)
            ->where('favoritable_id', $id)
            ->delete();   // دلوقتى ده Hard delete لأنه مفيش SoftDeletes
    }
    

    public function listForUser(int $userId, int $perPage = 15, ?string $favoritableType = null): LengthAwarePaginator
    {
        // ترجيع Paginator مطابق للـInterface مع تحميل العلاقات
        // Note: favoritable_type is stored as 'lawyer' or 'company' (string), not class name
        $query = Favorite::where('user_id', $userId);
        
        // Filter by favoritable_type if provided
        if ($favoritableType !== null) {
            $query->where('favoritable_type', $favoritableType);
        }
        
        $favorites = $query->orderByDesc('id')->paginate($perPage);

        // Manually load relationships based on favoritable_type
        // Group favorites by type for efficient loading
        $lawyerIds = [];
        $companyIds = [];
        
        foreach ($favorites->items() as $favorite) {
            if ($favorite->favoritable_type === 'lawyer') {
                $lawyerIds[] = $favorite->favoritable_id;
            } elseif ($favorite->favoritable_type === 'company') {
                $companyIds[] = $favorite->favoritable_id;
            }
        }

        // Load lawyers with relationships
        $lawyers = [];
        if (!empty($lawyerIds)) {
            $lawyers = Lawyer::whereIn('id', $lawyerIds)
                ->with(['user', 'specialties'])
                ->get()
                ->keyBy('id');
        }

        // Load companies with relationships
        $companies = [];
        if (!empty($companyIds)) {
            $companies = Company::whereIn('id', $companyIds)
                ->with(['owner', 'specialties'])
                ->get()
                ->keyBy('id');
        }

        // Attach the loaded models to favorites
        foreach ($favorites->items() as $favorite) {
            if ($favorite->favoritable_type === 'lawyer' && isset($lawyers[$favorite->favoritable_id])) {
                $favorite->setRelation('favoritable', $lawyers[$favorite->favoritable_id]);
            } elseif ($favorite->favoritable_type === 'company' && isset($companies[$favorite->favoritable_id])) {
                $favorite->setRelation('favoritable', $companies[$favorite->favoritable_id]);
            }
        }

        return $favorites;
    }
}
