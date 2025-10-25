<?php

namespace App\Modules\Reviews\Repositories\Eloquent;

use App\Modules\Reviews\Repositories\ReviewRepositoryInterface;
use App\Models\Review;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ReviewRepository implements ReviewRepositoryInterface
{
    public function create(array $data): Review
    {
        return Review::create($data);
    }

    public function forLawyer(int $lawyerId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->forTarget('lawyer', $lawyerId, $perPage);
    }

    public function forTarget(string $reviewableType, int $reviewableId, int $perPage = 15): LengthAwarePaginator
    {
        return Review::where('reviewable_type', $reviewableType)
            ->where('reviewable_id', $reviewableId)
            ->latest('posted_at')->paginate($perPage);
    }

    public function averageForLawyer(int $lawyerId): array
    {
        return $this->averageFor('lawyer', $lawyerId);
    }

    public function averageFor(string $reviewableType, int $reviewableId): array
    {
        $row = Review::select(DB::raw('AVG(rating) as avg, COUNT(*) as cnt'))
            ->where('reviewable_type', $reviewableType)
            ->where('reviewable_id', $reviewableId)->first();
        return ['avg' => (float)($row->avg ?? 0), 'count' => (int)($row->cnt ?? 0)];
    }

    public function existsForAppointment(int $appointmentId, int $reviewerId): bool
    {
        return Review::where('appointment_id', $appointmentId)
            ->where('reviewer_id', $reviewerId)->exists();
    }
}
