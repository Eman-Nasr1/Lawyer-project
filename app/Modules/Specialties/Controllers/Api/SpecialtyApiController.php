<?php

namespace App\Modules\Specialties\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Specialty;

class SpecialtyApiController extends Controller
{
    public function index()
    {
        $specialties = Specialty::select('id', 'name', 'slug')->orderBy('name')->get();

        return response()->json([
            'status' => true,
            'data' => $specialties
        ]);
    }
}
