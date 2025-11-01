<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lawyer;
use App\Models\Company;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * GET /api/most-experienced-lawyers
     * Get lawyers sorted by years of experience (descending)
     * Query Parameters:
     * - specialty_id: Filter by specialty ID (optional)
     * - per_page: Number of results per page (optional, default: 15)
     */
    public function mostExperiencedLawyers(Request $request)
    {
        $validated = $request->validate([
            'specialty_id' => ['nullable', 'integer', 'exists:specialties,id'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $perPage = (int) ($validated['per_page'] ?? 15);
        
        $query = Lawyer::where('is_approved', true)
            ->with(['user', 'specialties', 'primaryAddress', 'reviews.reviewer']);

        // Filter by specialty
        if (!empty($validated['specialty_id'])) {
            $query->whereHas('specialties', function ($q) use ($validated) {
                $q->where('specialties.id', $validated['specialty_id']);
            });
        }

        $lawyers = $query->orderBy('years_of_experience', 'desc')->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $lawyers
        ]);
    }

    /**
     * GET /api/most-experienced-companies
     * Get companies sorted by years of experience (descending)
     * Query Parameters:
     * - specialty_id: Filter by specialty ID (optional)
     * - per_page: Number of results per page (optional, default: 15)
     */
    public function mostExperiencedCompanies(Request $request)
    {
        $validated = $request->validate([
            'specialty_id' => ['nullable', 'integer', 'exists:specialties,id'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $perPage = (int) ($validated['per_page'] ?? 15);
        
        $query = Company::where('is_approved', true)
            ->with(['owner', 'specialties', 'primaryAddress', 'reviews.reviewer']);

        // Filter by specialty
        if (!empty($validated['specialty_id'])) {
            $query->whereHas('specialties', function ($q) use ($validated) {
                $q->where('specialties.id', $validated['specialty_id']);
            });
        }

        $companies = $query->orderBy('years_of_experience', 'desc')->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $companies
        ]);
    }

    /**
     * GET/POST /api/search
     * Search for lawyers or companies with filters:
     * - category: 'lawyers' or 'companies'
     * - specialty_id: filter by specialty
     * - experience_years: minimum years of experience
     * - min_rating: minimum rating (0-5)
     * - sort_by: 'rating' or 'experience' (default: 'experience')
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'category'         => ['nullable', 'in:all,lawyers,companies'], // فئات الواجهة
            'q'                => ['nullable', 'string', 'max:100'],        // كلمة البحث
            'specialty_id'     => ['nullable', 'integer', 'exists:specialties,id'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:100'],
            'min_rating'       => ['nullable', 'numeric', 'min:0', 'max:5'],
            'sort_by'          => ['nullable', 'in:rating,experience'],
            'per_page'         => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);
    
        $category   = $validated['category'] ?? 'all';
        $q          = trim($validated['q'] ?? '');
        $perPage    = (int)($validated['per_page'] ?? 15);
        $sortBy     = $validated['sort_by'] ?? 'experience';
        $specialty  = $validated['specialty_id'] ?? null;
        $minYears   = $validated['experience_years'] ?? null;
        $minRating  = $validated['min_rating'] ?? null;
    
        // ترتيب النتائج
        $order = function ($query) use ($sortBy) {
            return $sortBy === 'rating'
                ? $query->orderBy('avg_rating', 'desc')->orderBy('reviews_count', 'desc')
                : $query->orderBy('years_of_experience', 'desc');
        };
    
        // ---------- Lawyers ----------
        $lawyersQuery = Lawyer::query()
            ->where('is_approved', true)
            ->with(['user', 'specialties', 'primaryAddress', 'reviews.reviewer'])
            // البحث بالاسم في users.name عبر علاقة user
            ->when($q, fn($qq) =>
                $qq->whereHas('user', fn($uq) => $uq->where('name', 'like', "%{$q}%"))
            )
            ->when($specialty, fn($qq, $v) =>
                $qq->whereHas('specialties', fn($sq) => $sq->where('specialties.id', $v))
            )
            ->when($minYears !== null, fn($qq, $v) => $qq->where('years_of_experience', '>=', $v))
            ->when($minRating !== null, fn($qq, $v) => $qq->where('avg_rating', '>=', $v));
    
        $lawyersQuery = $order($lawyersQuery);
    
        // ---------- Companies ----------
        $companiesQuery = Company::query()
            ->where('is_approved', true)
            ->with(['owner', 'specialties', 'primaryAddress', 'reviews.reviewer'])
            // البحث بالاسم في users.name عبر علاقة owner
            ->when($q, fn($qq) =>
                $qq->whereHas('owner', fn($uq) => $uq->where('name', 'like', "%{$q}%"))
            )
            ->when($specialty, fn($qq, $v) =>
                $qq->whereHas('specialties', fn($sq) => $sq->where('specialties.id', $v))
            )
            ->when($minYears !== null, fn($qq, $v) => $qq->where('years_of_experience', '>=', $v))
            ->when($minRating !== null, fn($qq, $v) => $qq->where('avg_rating', '>=', $v));
    
        $companiesQuery = $order($companiesQuery);
    
        // الإرجاع حسب الفئة المختارة
        return match ($category) {
            'lawyers'   => response()->json([
                'status' => 'success',
                'data'   => [
                    'lawyers' => $lawyersQuery->paginate($perPage)
                ]
            ]),
            'companies' => response()->json([
                'status' => 'success',
                'data'   => [
                    'companies' => $companiesQuery->paginate($perPage)
                ]
            ]),
            default     => response()->json([
                'status' => 'success',
                'data'   => [
                    // أسماء بارام الصفحة مختلفة لتفادي تعارض الباجينيشن في الواجهة
                    'lawyers'   => $lawyersQuery->paginate($perPage, ['*'], 'lawyers_page'),
                    'companies' => $companiesQuery->paginate($perPage, ['*'], 'companies_page'),
                ]
            ]),
        };
    }
    

    /**
     * GET /api/highest-rated-lawyers
     * Get lawyers sorted by rating (descending)
     */
    public function highestRatedLawyers(Request $request)
    {
        $perPage = (int) $request->get('per_page', 15);
        
        $lawyers = Lawyer::where('is_approved', true)
            ->where('reviews_count', '>', 0) // Only show lawyers with reviews
            ->with(['user', 'specialties', 'primaryAddress', 'reviews.reviewer'])
            ->orderBy('avg_rating', 'desc')
            ->orderBy('reviews_count', 'desc')
            ->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $lawyers
        ]);
    }

    /**
     * GET /api/highest-rated-companies
     * Get companies sorted by rating (descending)
     */
    public function highestRatedCompanies(Request $request)
    {
        $perPage = (int) $request->get('per_page', 15);
        
        $companies = Company::where('is_approved', true)
            ->where('reviews_count', '>', 0) // Only show companies with reviews
            ->with(['owner', 'specialties', 'primaryAddress', 'reviews.reviewer'])
            ->orderBy('avg_rating', 'desc')
            ->orderBy('reviews_count', 'desc')
            ->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $companies
        ]);
    }
}

