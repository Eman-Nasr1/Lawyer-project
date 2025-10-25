<?php
namespace App\Modules\Reviews\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Reviews\Requests\StoreReviewRequest;
use App\Modules\Reviews\Services\ReviewService;

class ReviewClientController extends Controller
{
    public function __construct(private ReviewService $service) {}

    // POST /api/client/reviews
    public function store(StoreReviewRequest $req)
    {
        try {
            $r = $this->service->add(
                reviewerId:    $req->user()->id,
                appointmentId: (int)$req->appointment_id,
                targetType:    $req->target_type,
                targetId:      (int)$req->target_id,
                rating:        (int)$req->rating,
                comment:       $req->input('comment')
            );
            return response()->json($r, 201);
        } catch (\RuntimeException $e) {
            return response()->json(['message'=>$e->getMessage()], 422);
        }
    }
}
