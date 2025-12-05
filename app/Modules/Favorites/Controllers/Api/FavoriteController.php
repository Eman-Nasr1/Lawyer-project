<?php

namespace App\Modules\Favorites\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Favorites\Requests\ToggleFavoriteRequest;
use App\Modules\Favorites\Repositories\FavoriteRepositoryInterface;
use App\Modules\Favorites\Services\FavoriteService;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function __construct(
        private FavoriteRepositoryInterface $repo,
        private FavoriteService $service
    ) {}

    // POST /api/client/favorites/toggle
    // app/Modules/Favorites/Controllers/Api/FavoriteController.php
    public function toggle(ToggleFavoriteRequest $request)
    {
        $userId = $request->user()->id;
        try {
            return response()->json(
                $this->service->toggle($userId, $request->favoritable_type, (int)$request->favoritable_id)
            );
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    // GET /api/client/favorites
    public function index(Request $request)
    {
        // Validate favoritable_type filter
        $request->validate([
            'favoritable_type' => ['nullable', 'string', 'in:lawyer,company'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $favoritableType = $request->get('favoritable_type');
        $perPage = (int)$request->get('per_page', 15);
        
        $favorites = $this->repo->listForUser($request->user()->id, $perPage, $favoritableType);
        
        // Transform the data to include lawyer/company details
        $transformedFavorites = $favorites->through(function ($favorite) {
            $favoritable = $favorite->favoritable;
            $data = [
                'id' => $favorite->id,
                'favoritable_type' => $favorite->favoritable_type,
                'favoritable_id' => $favorite->favoritable_id,
                'created_at' => $favorite->created_at,
                'updated_at' => $favorite->updated_at,
            ];

            if ($favoritable) {
                if ($favorite->favoritable_type === 'lawyer' && $favoritable instanceof \App\Models\Lawyer) {
                    $data['lawyer'] = [
                        'id' => $favoritable->id,
                        'years_of_experience' => $favoritable->years_of_experience,
                        'bio' => $favoritable->bio,
                        'avg_rating' => $favoritable->avg_rating,
                        'reviews_count' => $favoritable->reviews_count,
                        'is_approved' => $favoritable->is_approved,
                        'is_featured' => $favoritable->is_featured,
                        'professional_card_image' => $favoritable->professional_card_image,
                        'professional_card_image_url' => $favoritable->professional_card_image_url,
                        'user' => $favoritable->user ? [
                            'id' => $favoritable->user->id,
                            'name' => $favoritable->user->name,
                            'email' => $favoritable->user->email,
                            'phone' => $favoritable->user->phone,
                            'avatar' => $favoritable->user->avatar,
                            'avatar_url' => $favoritable->user->avatar_url,
                            'type' => $favoritable->user->type,
                        ] : null,
                        'specialties' => $favoritable->specialties ? $favoritable->specialties->map(function ($specialty) {
                            return [
                                'id' => $specialty->id,
                                'name' => $specialty->name,
                                'slug' => $specialty->slug,
                            ];
                        }) : [],
                    ];
                } elseif ($favorite->favoritable_type === 'company' && $favoritable instanceof \App\Models\Company) {
                    $data['company'] = [
                        'id' => $favoritable->id,
                        'years_of_experience' => $favoritable->years_of_experience,
                        'description' => $favoritable->description,
                        'avg_rating' => $favoritable->avg_rating,
                        'reviews_count' => $favoritable->reviews_count,
                        'is_approved' => $favoritable->is_approved,
                        'is_featured' => $favoritable->is_featured,
                        'professional_card_image' => $favoritable->professional_card_image,
                        'professional_card_image_url' => $favoritable->professional_card_image_url,
                        'user' => $favoritable->owner ? [
                            'id' => $favoritable->owner->id,
                            'name' => $favoritable->owner->name,
                            'email' => $favoritable->owner->email,
                            'phone' => $favoritable->owner->phone,
                            'avatar' => $favoritable->owner->avatar,
                            'avatar_url' => $favoritable->owner->avatar_url,
                            'type' => $favoritable->owner->type,
                        ] : null,
                        'specialties' => $favoritable->specialties ? $favoritable->specialties->map(function ($specialty) {
                            return [
                                'id' => $specialty->id,
                                'name' => $specialty->name,
                                'slug' => $specialty->slug,
                            ];
                        }) : [],
                    ];
                }
            }

            return $data;
        });

        return response()->json($transformedFavorites);
    }
}
