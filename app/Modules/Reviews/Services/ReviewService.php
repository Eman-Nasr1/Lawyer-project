<?php
namespace App\Modules\Reviews\Services;

use App\Modules\Reviews\Repositories\ReviewRepositoryInterface;
use App\Models\Appointment;
use App\Models\Lawyer;

class ReviewService
{
    public function __construct(private ReviewRepositoryInterface $repo) {}

  // app/Modules/Reviews/Services/ReviewService.php
private function mapType(string $type): string {
    return match($type){
      'lawyer'  => \App\Models\Lawyer::class,
      'company' => \App\Models\Company::class,
    };
  }
  
  public function add(
    int $reviewerId, int $appointmentId, string $targetType, int $targetId,
    int $rating, ?string $comment
  ){
    $a = \App\Models\Appointment::findOrFail($appointmentId);
    if ($a->user_id !== $reviewerId) {
      throw new \RuntimeException('You can review only your own appointment.');
    }
  
    $morph = $this->mapType($targetType);
  
    if ($targetType === 'lawyer') {
      if ((int)$a->lawyer_id !== $targetId) throw new \RuntimeException('Appointment not with this lawyer.');
    } else { // company
      if (empty($a->company_id) || (int)$a->company_id !== $targetId) {
        throw new \RuntimeException('Appointment not with this company.');
      }
    }
  
    if ($this->repo->existsForAppointment($appointmentId, $reviewerId)) {
      throw new \RuntimeException('You already reviewed this appointment.');
    }
  
    $review = $this->repo->create([
      'appointment_id' => $appointmentId,
      'reviewer_id'    => $reviewerId,
      'reviewable_type'=> $targetType,
      'reviewable_id'  => $targetId,
      'rating'         => $rating,
      'comment'        => $comment,
      'posted_at'      => now(),
    ]);
  
    // update aggregates
    $agg = $this->repo->averageFor($targetType, $targetId);
    if ($targetType === 'lawyer') {
      \App\Models\Lawyer::whereKey($targetId)->update([
        'avg_rating' => round($agg['avg'],2),
        'reviews_count' => $agg['count'],
      ]);
    } else {
      \App\Models\Company::whereKey($targetId)->update([
        'avg_rating' => round($agg['avg'],2),
        'reviews_count' => $agg['count'],
      ]);
    }
  
    return $review;
  }

  public function addForLawyer(
    int $reviewerId, int $appointmentId, int $lawyerId,
    int $rating, ?string $comment
  ) {
    return $this->add($reviewerId, $appointmentId, 'lawyer', $lawyerId, $rating, $comment);
  }
  
}
