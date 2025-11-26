<?php

namespace App\Modules\Lawyer\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lawyer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LawyerDirectoryController extends Controller
{
    /**
     * GET /api/lawyers
     * List lawyers with filters and sorting
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'specialty_id' => ['nullable', 'integer', 'exists:specialties,id'],
            'city_id' => ['nullable'], // Can be integer (if you have city IDs) or string (city name)
            'governorate_id' => ['nullable'],
            'search' => ['nullable', 'string', 'max:255'],
            'sort' => ['nullable', 'string', 'in:top-rated,most-reviewed'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $perPage = (int) ($validated['per_page'] ?? 15);
        $userId = Auth::id();

        $query = Lawyer::where('is_approved', true)
            ->with(['user', 'specialties', 'primaryAddress']);

        // Filter by specialty
        if (!empty($validated['specialty_id'])) {
            $query->whereHas('specialties', function ($q) use ($validated) {
                $q->where('specialties.id', $validated['specialty_id']);
            });
        }

        // Filter by city (from address) - city is stored as string in addresses table
        if (!empty($validated['city_id'])) {
            $cityValue = $validated['city_id'];
            $query->whereHas('addresses', function ($q) use ($cityValue) {
                if (is_numeric($cityValue)) {
                    // If numeric, treat as exact match (in case you have numeric city codes)
                    $q->where('city', $cityValue);
                } else {
                    // If string, use LIKE for partial matching
                    $q->where('city', 'like', "%{$cityValue}%");
                }
            });
        }

        // Search by name
        if (!empty($validated['search'])) {
            $search = $validated['search'];
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }
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
        $lawyers = $query->paginate($perPage);

        // Transform the data
        $lawyers->getCollection()->transform(function ($lawyer) use ($userId) {
            $primaryAddress = $lawyer->primaryAddress;
            
            // Check if favorite
            $isFavorite = false;
            if ($userId) {
                $isFavorite = $lawyer->favorites()->where('user_id', $userId)->exists();
            }

            return [
                'id' => $lawyer->id,
                'name' => $lawyer->user->name,
                'avatar_url' => $lawyer->user->avatar_url,
                'specialties' => $lawyer->specialties->map(function ($specialty) {
                    return [
                        'id' => $specialty->id,
                        'name' => $specialty->name,
                        'slug' => $specialty->slug,
                    ];
                }),
                'years_of_experience' => $lawyer->years_of_experience,
                'address' => $primaryAddress ? [
                    'city' => $primaryAddress->city,
                    'area' => $primaryAddress->address_line,
                    'full_address' => $this->formatAddress($primaryAddress),
                ] : null,
                'avg_rating' => (float) $lawyer->avg_rating,
                'reviews_count' => (int) $lawyer->reviews_count,
                'cases_count' => null, // Add if you have this field
                'clients_count' => null, // Add if you have this field
                'is_favorite' => $isFavorite,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $lawyers
        ]);
    }

    /**
     * GET /api/lawyers/{id}
     * Get lawyer details
     */
    public function show($id)
    {
        $lawyer = Lawyer::where('is_approved', true)
            ->with([
                'user',
                'specialties',
                'primaryAddress',
                'addresses',
                'reviews.reviewer' => function ($q) {
                    $q->select('id', 'name', 'avatar');
                }
            ])
            ->findOrFail($id);

        $userId = Auth::id();
        $isFavorite = false;
        if ($userId) {
            $isFavorite = $lawyer->favorites()->where('user_id', $userId)->exists();
        }

        // Get working days and hours from availability
        $availabilities = \App\Models\LawyerAvailability::where('lawyer_id', $lawyer->id)
            ->where('is_active', true)
            ->get()
            ->map(function ($av) {
                return [
                    'day_of_week' => $av->day_of_week,
                    'date' => $av->date,
                    'start_time' => $av->start_time,
                    'end_time' => $av->end_time,
                ];
            });

        // Get latest reviews
        $latestReviews = $lawyer->reviews()
            ->with('reviewer:id,name,avatar')
            ->latest('posted_at')
            ->limit(10)
            ->get()
            ->map(function ($review) {
                return [
                    'id' => $review->id,
                    'reviewer_name' => $review->reviewer->name ?? 'Anonymous',
                    'reviewer_avatar' => $review->reviewer->avatar_url ?? null,
                    'rating' => (int) $review->rating,
                    'comment' => $review->comment,
                    'date' => $review->posted_at ? $review->posted_at->format('Y-m-d H:i:s') : $review->created_at->format('Y-m-d H:i:s'),
                ];
            });

        $primaryAddress = $lawyer->primaryAddress;

        $data = [
            'id' => $lawyer->id,
            'name' => $lawyer->user->name,
            'avatar_url' => $lawyer->user->avatar_url,
            'specialties' => $lawyer->specialties->map(function ($specialty) {
                return [
                    'id' => $specialty->id,
                    'name' => $specialty->name,
                    'slug' => $specialty->slug,
                ];
            }),
            'bio' => $lawyer->bio,
            'years_of_experience' => $lawyer->years_of_experience,
            'stats' => [
                'avg_rating' => (float) $lawyer->avg_rating,
                'reviews_count' => (int) $lawyer->reviews_count,
                'cases_count' => null, // Add if you have this field
                'clients_count' => null, // Add if you have this field
            ],
            'working_days_hours' => $availabilities,
            'address' => $primaryAddress ? [
                'full_address' => $this->formatAddress($primaryAddress),
                'city' => $primaryAddress->city,
                'area' => $primaryAddress->address_line,
                'lat' => $primaryAddress->lat ? (float) $primaryAddress->lat : null,
                'lng' => $primaryAddress->lng ? (float) $primaryAddress->lng : null,
            ] : null,
            'latest_reviews' => $latestReviews,
            'is_favorite' => $isFavorite,
        ];

        return response()->json([
            'status' => true,
            'data' => $data
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

