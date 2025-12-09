<?php
namespace App\Modules\Lawyer\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Lawyer\Requests\AvailabilityRequest;
use App\Modules\Lawyer\Services\LawyerAvailabilityService;
use App\Modules\Lawyer\Repositories\LawyerAvailabilityRepositoryInterface;
use Illuminate\Http\Request;

class LawyerAvailabilityController extends Controller
{
    public function __construct(
        private LawyerAvailabilityService $service,
        private LawyerAvailabilityRepositoryInterface $repo
    ){}

    // GET /api/lawyer/availabilities
    public function index(Request $request)
    {
        $lawyer = $request->user()->lawyer;
        
        if (!$lawyer) {
            return response()->json(['message' => 'No lawyer profile for this user'], 403);
        }
        
        $perPage = (int) $request->get('per_page', 15);
        $availabilities = $this->repo->paginateForLawyer($lawyer->id, $perPage);
        
        return response()->json([
            'status' => true,
            'data' => $availabilities
        ]);
    }

    // POST /api/lawyer/availabilities
    public function store(AvailabilityRequest $request)
    {
        $lawyer = $request->user()->lawyer;
        
        if (!$lawyer) {
            return response()->json(['message' => 'No lawyer profile for this user'], 403);
        }
        
        $data = $request->validated();
        // Remove id if present to ensure it's a create operation
        unset($data['id']);

        try {
            $model = $this->service->upsertForLawyer($lawyer->id, $data);
            return response()->json($model, 201);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    // PUT /api/lawyer/availabilities/{id}
    public function update(AvailabilityRequest $request, int $id)
    {
        $lawyer = $request->user()->lawyer;
        
        if (!$lawyer) {
            return response()->json(['message' => 'No lawyer profile for this user'], 403);
        }
        
        // Verify the availability belongs to this lawyer
        $availability = $this->repo->find($id);
        if ($availability->lawyer_id !== $lawyer->id) {
            return response()->json(['message' => 'Availability not found or access denied'], 403);
        }
        
        $data = $request->validated();
        $data['id'] = $id;

        try {
            $model = $this->service->upsertForLawyer($lawyer->id, $data);
            return response()->json($model, 200);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    // DELETE /api/lawyer/availabilities/{id}
    public function destroy(Request $request, int $id)
    {
        $lawyer = $request->user()->lawyer;
        
        if (!$lawyer) {
            return response()->json(['message' => 'No lawyer profile for this user'], 403);
        }
        
        // Verify the availability belongs to this lawyer
        $availability = $this->repo->find($id);
        if ($availability->lawyer_id !== $lawyer->id) {
            return response()->json(['message' => 'Availability not found or access denied'], 403);
        }
        
        $this->repo->delete($id);
        
        return response()->json([
            'status' => true,
            'message' => 'Availability deleted successfully'
        ]);
    }
}

