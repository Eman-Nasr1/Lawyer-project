<?php
namespace App\Modules\Company\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Company\Requests\AvailabilityRequest;
use App\Modules\Company\Services\CompanyAvailabilityService;
use Illuminate\Http\Request;

class CompanyAvailabilityUpsertController extends Controller
{
    public function __construct(private CompanyAvailabilityService $service){}

    // POST /api/company/availabilities/upsert
    public function upsert(AvailabilityRequest $request)
    {
        $user = $request->user();
        
        if (!$user->company) {
            return response()->json(['message' => 'No company profile for this user'], 403);
        }
        
        $companyId = $user->company->id;
        $data = $request->validated();

        try {
            $model = $this->service->upsertForCompany($companyId, $data);
            return response()->json($model, !empty($data['id']) ? 200 : 201);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}

