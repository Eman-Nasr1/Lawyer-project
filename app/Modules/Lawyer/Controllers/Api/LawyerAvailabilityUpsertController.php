<?php
namespace App\Modules\Lawyer\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Lawyer\Requests\AvailabilityRequest;
use App\Modules\Lawyer\Services\LawyerAvailabilityService;
use Illuminate\Http\Request;

class LawyerAvailabilityUpsertController extends Controller
{
    public function __construct(private LawyerAvailabilityService $service){}

    // POST /api/lawyer/availabilities/upsert
    public function upsert(AvailabilityRequest $request)
    {
        $lawyerId = $request->user()->lawyer->id; // تأكدي من العلاقة
        $data = $request->validated();

        try {
            $model = $this->service->upsertForLawyer($lawyerId, $data);
            return response()->json($model, !empty($data['id']) ? 200 : 201);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
