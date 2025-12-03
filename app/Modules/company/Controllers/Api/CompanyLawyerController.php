<?php

namespace App\Modules\Company\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Lawyer;
use Illuminate\Http\Request;

class CompanyLawyerController extends Controller
{
    // GET /api/company/lawyers
    public function index(Request $request)
    {
        $user = $request->user();
    
        if (!$user->company) {
            return response()->json(['message' => 'No company profile for this user'], 403);
        }
    
        $company  = $user->company;
        $perPage  = (int) $request->get('per_page', 15);
    
        $lawyers = $company->lawyers()
            ->with('user', 'specialties')
            ->paginate($perPage);
    
        $lawyers->getCollection()->transform(function ($lawyer) {
            return [
                'id'   => $lawyer->id,
                'name' => $lawyer->user->name ?? null,
                'avatar_url' => $lawyer->user->avatar_url ?? null,
                'specialties' => $lawyer->specialties->map(function ($s) {
                    return [
                        'id'   => $s->id,
                        'name' => $s->name,
                        'slug' => $s->slug,
                    ];
                }),
                'years_of_experience' => $lawyer->years_of_experience,
            ];
        });
    
        return response()->json([
            'status' => true,
            'data'   => $lawyers,
        ]);
    }
    

    // POST /api/company/lawyers/attach
    // body: { "lawyer_id": 5 }
    public function attach(Request $request)
    {
        $validated = $request->validate([
            'lawyer_id' => ['required', 'integer', 'exists:lawyers,id'],
            'title'     => ['nullable', 'string', 'max:255'],
            'is_primary'=> ['nullable', 'boolean'],
        ]);
    
        $user = $request->user();
    
        if (!$user->company) {
            return response()->json(['message' => 'No company profile for this user'], 403);
        }
    
        $company = $user->company;
    
        $lawyer = Lawyer::findOrFail($validated['lawyer_id']);
    
        // ✅ لو عايزة كل محامى يكون ليه شركة واحدة فقط
        if ($lawyer->companies()->exists()) {
            return response()->json([
                'status'  => false,
                'message' => 'This lawyer is already attached to another company.',
            ], 422);
        }
    
        $company->lawyers()->syncWithoutDetaching([
            $lawyer->id => [
                'title'      => $validated['title'] ?? null,
                'is_primary' => $validated['is_primary'] ?? true,
            ],
        ]);
    
        return response()->json([
            'status'  => true,
            'message' => 'Lawyer attached to company successfully',
        ]);
    }
    
    

    // DELETE /api/company/lawyers/{lawyer}
    public function detach(Request $request, int $lawyerId)
    {
        $user = $request->user();
    
        if (!$user->company) {
            return response()->json(['message' => 'No company profile for this user'], 403);
        }
    
        $company = $user->company;
    
        // هنمسح من جدول الـ pivot
        $company->lawyers()->detach($lawyerId);
    
        return response()->json([
            'status'  => true,
            'message' => 'Lawyer detached from company successfully',
        ]);
    }
    
}
