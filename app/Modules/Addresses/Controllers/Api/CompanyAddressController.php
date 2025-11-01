<?php

namespace App\Modules\Addresses\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Addresses\Requests\AddressRequest;
use App\Modules\Addresses\Services\AddressService;
use Illuminate\Http\Request;

class CompanyAddressController extends Controller
{
    public function __construct(private AddressService $service) {}

    /**
     * GET /api/company/addresses
     * Get all addresses for the authenticated company
     */
    public function index(Request $request)
    {
        $company = $request->user()->company;
        
        if (!$company) {
            return response()->json([
                'status' => 'error',
                'message' => 'User is not a company'
            ], 403);
        }

        $perPage = (int) $request->get('per_page', 15);
        $addresses = $this->service->listForCompany($company->id, $perPage);

        return response()->json([
            'status' => 'success',
            'data' => $addresses
        ]);
    }

    /**
     * POST /api/company/addresses
     * Create a new address for the authenticated company
     */
    public function store(AddressRequest $request)
    {
        $company = $request->user()->company;
        
        if (!$company) {
            return response()->json([
                'status' => 'error',
                'message' => 'User is not a company'
            ], 403);
        }

        $address = $this->service->createForCompany($company->id, $request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Address created successfully',
            'data' => $address
        ], 201);
    }

    /**
     * GET /api/company/addresses/{id}
     * Get a specific address
     */
    public function show(Request $request, int $id)
    {
        $company = $request->user()->company;
        
        if (!$company) {
            return response()->json([
                'status' => 'error',
                'message' => 'User is not a company'
            ], 403);
        }

        $address = $this->service->get($id);

        // Verify the address belongs to this company
        if ($address->addressable_type !== 'company' || 
            $address->addressable_id !== $company->id) {
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
     * PUT/PATCH /api/company/addresses/{id}
     * Update an address
     */
    public function update(AddressRequest $request, int $id)
    {
        $company = $request->user()->company;
        
        if (!$company) {
            return response()->json([
                'status' => 'error',
                'message' => 'User is not a company'
            ], 403);
        }

        $address = $this->service->get($id);

        // Verify the address belongs to this company
        if ($address->addressable_type !== 'company' || 
            $address->addressable_id !== $company->id) {
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
     * DELETE /api/company/addresses/{id}
     * Delete an address
     */
    public function destroy(Request $request, int $id)
    {
        $company = $request->user()->company;
        
        if (!$company) {
            return response()->json([
                'status' => 'error',
                'message' => 'User is not a company'
            ], 403);
        }

        $address = $this->service->get($id);

        // Verify the address belongs to this company
        if ($address->addressable_type !== 'company' || 
            $address->addressable_id !== $company->id) {
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
     * POST /api/company/addresses/{id}/set-primary
     * Set an address as primary
     */
    public function setPrimary(Request $request, int $id)
    {
        $company = $request->user()->company;
        
        if (!$company) {
            return response()->json([
                'status' => 'error',
                'message' => 'User is not a company'
            ], 403);
        }

        $address = $this->service->get($id);

        // Verify the address belongs to this company
        if ($address->addressable_type !== 'company' || 
            $address->addressable_id !== $company->id) {
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

