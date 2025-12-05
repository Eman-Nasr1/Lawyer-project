<?php

namespace App\Modules\Company\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyDirectoryController extends Controller
{
    /**
     * GET /api/companies
     * List companies with filters and sorting
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'specialty_id'   => ['nullable', 'integer', 'exists:specialties,id'],
            'city_id'        => ['nullable'], // ممكن يكون رقم أو نص
            'governorate_id' => ['nullable'],
            'search'         => ['nullable', 'string', 'max:255'],
            'sort'           => ['nullable', 'string', 'in:top-rated,most-reviewed'],
            'per_page'       => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $perPage = (int) ($validated['per_page'] ?? 15);
        $userId  = Auth::id();

        $query = Company::approved()
            ->with(['owner', 'specialties', 'primaryAddress']);

        // Filter by specialty
        if (!empty($validated['specialty_id'])) {
            $query->whereHas('specialties', function ($q) use ($validated) {
                $q->where('specialties.id', $validated['specialty_id']);
            });
        }

        // Filter by city (from polymorphic addresses)
        if (!empty($validated['city_id'])) {
            $cityValue = $validated['city_id'];

            $query->whereHas('addresses', function ($q) use ($cityValue) {
                if (is_numeric($cityValue)) {
                    $q->where('city', $cityValue);
                } else {
                    $q->where('city', 'like', "%{$cityValue}%");
                }
            });
        }

        // Filter by governorate_id لو موجود في جدول العناوين
        if (!empty($validated['governorate_id'])) {
            $govValue = $validated['governorate_id'];

            $query->whereHas('addresses', function ($q) use ($govValue) {
                $q->where('governorate_id', $govValue);
            });
        }

        // Search by company name أو اسم الـ owner
        if (!empty($validated['search'])) {
            $search = $validated['search'];

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('owner', function ($sub) use ($search) {
                      $sub->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Sorting
        $sort = $validated['sort'] ?? null;
        if ($sort === 'top-rated') {
            $query->orderBy('avg_rating', 'desc')
                  ->orderBy('reviews_count', 'desc');
        } elseif ($sort === 'most-reviewed') {
            $query->orderBy('reviews_count', 'desc')
                  ->orderBy('avg_rating', 'desc');
        } else {
            $query->orderBy('id', 'desc');
        }

        $companies = $query->paginate($perPage);

        // Transform data
        $transformedCompanies = $companies->through(function ($company) use ($userId) {
            $primaryAddress = $company->primaryAddress;
        
            // Is favorite
            $isFavorite = false;
            if ($userId) {
                $isFavorite = $company->favorites()
                    ->where('user_id', $userId)
                    ->exists();
            }
        
            return [
                'id'   => $company->owner->id,
                'user_id' => $company->owner->id,
                'company_id' => $company->id,
                'name' => $company->owner->name,                 // اسم الشركة
                'avatar_url' => $company->owner->avatar_url, 
                      // زي المحامي، لكن هنا لوجو الشركة
                'specialties' => $company->specialties->map(function ($specialty) {
                    return [
                        'id'   => $specialty->id,
                        'name' => $specialty->name,
                        'slug' => $specialty->slug,
                    ];
                }),
        
                'years_of_experience' => $company->years_of_experience,
        
                'address' => $primaryAddress ? [
                    'city'         => $primaryAddress->city,
                    'area'         => $primaryAddress->address_line,
                    'full_address' => $this->formatAddress($primaryAddress),
                ] : null,
        
                'avg_rating'    => (float) ($company->avg_rating ?? 0),
                'reviews_count' => (int) ($company->reviews_count ?? 0),
        
                // ممكن لو حابة تضيفي دول بعدين زى المحامى
                'cases_count'   => null,
                'clients_count' => null,
        
                'is_favorite' => $isFavorite,
            ];
        });

        return response()->json([
            'status' => true,
            'data'   => $transformedCompanies,
        ]);
    }

    /**
     * GET /api/companies/{id}
     * Show company details
     */
    public function show($id)
    {
        $company = Company::approved()
        ->where('user_id', $id)   // 👈 هنا بنستخدم user_id بدل company id
        ->with([
            'owner',
            'specialties',
            'primaryAddress',
            'addresses',
            'reviews.reviewer' => function ($q) {
                $q->select('id', 'name', 'avatar');
            },
            'lawyers.user',
        ])
        ->firstOrFail();
    

        $userId = Auth::id();

        $isFavorite = false;
        if ($userId) {
            $isFavorite = $company->favorites()
                ->where('user_id', $userId)
                ->exists();
        }

        // Latest reviews
        $latestReviews = $company->reviews()
            ->with('reviewer:id,name,avatar')
            ->latest('posted_at')
            ->limit(10)
            ->get()
            ->map(function ($review) {
                return [
                    'id'              => $review->id,
                    'reviewer_name'   => $review->reviewer->name ?? 'Anonymous',
                    'reviewer_avatar' => $review->reviewer->avatar_url ?? null,
                    'rating'          => (int) $review->rating,
                    'comment'         => $review->comment,
                    'date'            => $review->posted_at
                        ? $review->posted_at->format('Y-m-d H:i:s')
                        : $review->created_at->format('Y-m-d H:i:s'),
                ];
            });

        $primaryAddress = $company->primaryAddress;

        $data = [
            'id'          => $company->owner->id,
            'name'        => $company->owner->name,
            'avatar_url'  => $company->owner->avatar_url,  // نفس اسم الفيلد اللى الفرونت متعود عليه
            'professional_card_image_url' => $company->professional_card_image_url,
            'description' => $company->description,
            'years_of_experience' => $company->years_of_experience,
        
            // ❌ شلنا بلوك owner
        
            'specialties' => $company->specialties->map(function ($specialty) {
                return [
                    'id'   => $specialty->id,
                    'name' => $specialty->name,
                    'slug' => $specialty->slug,
                ];
            }),
        
            'stats' => [
                'avg_rating'    => (float) ($company->avg_rating ?? 0),
                'reviews_count' => (int) ($company->reviews_count ?? 0),
            ],
        
            'working_days_hours' => [],
        
            'address' => $primaryAddress ? [
                'full_address' => $this->formatAddress($primaryAddress),
                'city'         => $primaryAddress->city,
                'area'         => $primaryAddress->address_line,
                'lat'          => $primaryAddress->lat ? (float) $primaryAddress->lat : null,
                'lng'          => $primaryAddress->lng ? (float) $primaryAddress->lng : null,
            ] : null,
        
            'lawyers' => $company->lawyers->map(function ($lawyer) {
                return [
                    'id'         => $lawyer->id,
                    'name'       => $lawyer->user->name ?? null,
                    'avatar_url' => $lawyer->user->avatar_url ?? null,
                ];
            }),
        
            'latest_reviews' => $latestReviews,
            'is_favorite'    => $isFavorite,
        ];
        
        return response()->json([
            'status' => true,
            'data'   => $data,
        ]);
    }

    private function formatAddress($address)
    {
        $parts = array_filter([
            $address->address_line,
            $address->building_number ? 'Building ' . $address->building_number : null,
            $address->floor_number ? 'Floor ' . $address->floor_number : null,
            $address->apartment_number ? 'Apartment ' . $address->apartment_number : null,
            $address->city,
        ]);

        return implode(', ', $parts) ?: null;
    }
}
