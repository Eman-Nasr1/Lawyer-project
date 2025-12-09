<?php
namespace App\Modules\Company\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Company\Requests\AvailabilityRequest;
use App\Modules\Company\Services\CompanyAvailabilityService;
use App\Modules\Company\Repositories\CompanyAvailabilityRepositoryInterface;
use Illuminate\Http\Request;

class CompanyAvailabilityController extends Controller
{
    public function __construct(
        private CompanyAvailabilityService $service,
        private CompanyAvailabilityRepositoryInterface $repo
    ){}

    // GET /api/company/availabilities
    public function index(Request $request)
    {
        $user = $request->user();
        
        if (!$user->company) {
            return response()->json(['message' => 'No company profile for this user'], 403);
        }
        
        $perPage = (int) $request->get('per_page', 15);
        $availabilities = $this->repo->paginateForCompany($user->company->id, $perPage);
        
        return response()->json([
            'status' => true,
            'data' => $availabilities
        ]);
    }

    // POST /api/company/availabilities
    public function store(AvailabilityRequest $request)
    {
        $user = $request->user();
        
        if (!$user->company) {
            return response()->json(['message' => 'No company profile for this user'], 403);
        }
        
        $data = $request->validated();
        // Remove id if present to ensure it's a create operation
        unset($data['id']);

        try {
            $model = $this->service->upsertForCompany($user->company->id, $data);
            return response()->json($model, 201);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    // PUT /api/company/availabilities/{id}
    public function update(AvailabilityRequest $request, int $id)
    {
        $user = $request->user();
        
        if (!$user->company) {
            return response()->json(['message' => 'No company profile for this user'], 403);
        }
        
        // Verify the availability belongs to this company
        $availability = $this->repo->find($id);
        if ($availability->company_id !== $user->company->id) {
            return response()->json(['message' => 'Availability not found or access denied'], 403);
        }
        
        $data = $request->validated();
        $data['id'] = $id;

        try {
            $model = $this->service->upsertForCompany($user->company->id, $data);
            return response()->json($model, 200);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    // DELETE /api/company/availabilities/{id}
    public function destroy(Request $request, int $id)
    {
        $user = $request->user();
        
        if (!$user->company) {
            return response()->json(['message' => 'No company profile for this user'], 403);
        }
        
        // Verify the availability belongs to this company
        $availability = $this->repo->find($id);
        if ($availability->company_id !== $user->company->id) {
            return response()->json(['message' => 'Availability not found or access denied'], 403);
        }
        
        $this->repo->delete($id);
        
        return response()->json([
            'status' => true,
            'message' => 'Availability deleted successfully'
        ]);
    }
}

