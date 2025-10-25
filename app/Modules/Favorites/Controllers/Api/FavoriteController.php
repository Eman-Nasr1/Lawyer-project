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
        return response()->json(
            $this->repo->listForUser($request->user()->id, (int)$request->get('per_page', 15))
        );
    }
}
