<?php

namespace App\Modules\Addresses\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Addresses\Requests\AddressRequest;
use App\Modules\Addresses\Services\AddressService;
use Illuminate\Http\Request;

class LawyerAddressController extends Controller
{
    public function __construct(private AddressService $service) {}

    /**
     * GET /api/lawyer/addresses
     * Get all addresses for the authenticated lawyer
     */
    public function index(Request $request)
    {
        $lawyer = $request->user()->lawyer;
        
        if (!$lawyer) {
            return response()->json([
                'status' => 'error',
                'message' => 'User is not a lawyer'
            ], 403);
        }

        $perPage = (int) $request->get('per_page', 15);
        $addresses = $this->service->listForLawyer($lawyer->id, $perPage);

        return response()->json([
            'status' => 'success',
            'data' => $addresses
        ]);
    }

    /**
     * POST /api/lawyer/addresses
     * Create a new address for the authenticated lawyer
     */
    public function store(AddressRequest $request)
    {
        $lawyer = $request->user()->lawyer;
        
        if (!$lawyer) {
            return response()->json([
                'status' => 'error',
                'message' => 'User is not a lawyer'
            ], 403);
        }

        $address = $this->service->createForLawyer($lawyer->id, $request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Address created successfully',
            'data' => $address
        ], 201);
    }

    /**
     * GET /api/lawyer/addresses/{id}
     * Get a specific address
     */
    public function show(Request $request, int $id)
    {
        $lawyer = $request->user()->lawyer;
        
        if (!$lawyer) {
            return response()->json([
                'status' => 'error',
                'message' => 'User is not a lawyer'
            ], 403);
        }

        $address = $this->service->get($id);

        // Verify the address belongs to this lawyer
        if ($address->addressable_type !== 'lawyer' || 
            $address->addressable_id !== $lawyer->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Address not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $address
        ]);
    }

    /**
     * PUT/PATCH /api/lawyer/addresses/{id}
     * Update an address
     */
    public function update(AddressRequest $request, int $id)
    {
        $lawyer = $request->user()->lawyer;
        
        if (!$lawyer) {
            return response()->json([
                'status' => 'error',
                'message' => 'User is not a lawyer'
            ], 403);
        }

        $address = $this->service->get($id);

        // Verify the address belongs to this lawyer
        if ($address->addressable_type !== 'lawyer' || 
            $address->addressable_id !== $lawyer->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Address not found'
            ], 404);
        }

        $updated = $this->service->update($id, $request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Address updated successfully',
            'data' => $updated
        ]);
    }

    /**
     * DELETE /api/lawyer/addresses/{id}
     * Delete an address
     */
    public function destroy(Request $request, int $id)
    {
        $lawyer = $request->user()->lawyer;
        
        if (!$lawyer) {
            return response()->json([
                'status' => 'error',
                'message' => 'User is not a lawyer'
            ], 403);
        }

        $address = $this->service->get($id);

        // Verify the address belongs to this lawyer
        if ($address->addressable_type !== 'lawyer' || 
            $address->addressable_id !== $lawyer->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Address not found'
            ], 404);
        }

        $this->service->delete($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Address deleted successfully'
        ]);
    }

    /**
     * POST /api/lawyer/addresses/{id}/set-primary
     * Set an address as primary
     */
    public function setPrimary(Request $request, int $id)
    {
        $lawyer = $request->user()->lawyer;
        
        if (!$lawyer) {
            return response()->json([
                'status' => 'error',
                'message' => 'User is not a lawyer'
            ], 403);
        }

        $address = $this->service->get($id);

        // Verify the address belongs to this lawyer
        if ($address->addressable_type !== 'lawyer' || 
            $address->addressable_id !== $lawyer->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Address not found'
            ], 404);
        }

        $this->service->setAsPrimary($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Address set as primary successfully',
            'data' => $this->service->get($id)
        ]);
    }
}

