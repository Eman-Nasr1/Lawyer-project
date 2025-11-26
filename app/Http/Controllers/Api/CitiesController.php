<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;

class CitiesController extends Controller
{
    /**
     * GET /api/cities
     * Get all active cities with their country
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'country_id' => ['nullable', 'integer', 'exists:countries,id'],
        ]);

        $query = City::with('country:id,name,code')
            ->select('id', 'country_id', 'name', 'status');

        // Filter by country if provided
        if (!empty($validated['country_id'])) {
            $query->where('country_id', $validated['country_id']);
        }

        // Filter active cities (assuming status field indicates active/inactive)
        // Adjust based on your status field values
        $cities = $query->where('status', 'active')
            ->orderBy('name')
            ->get()
            ->map(function ($city) {
                return [
                    'id' => $city->id,
                    'name' => $city->name,
                    'country' => $city->country ? [
                        'id' => $city->country->id,
                        'name' => $city->country->name,
                        'code' => $city->country->code,
                    ] : null,
                ];
            });

        return response()->json([
            'status' => true,
            'data' => $cities
        ]);
    }
}

