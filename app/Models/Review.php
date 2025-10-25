<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'appointment_id','reviewer_id',
        'reviewable_type','reviewable_id',
        'rating','comment','posted_at'
    ];

    protected $casts = ['posted_at'=>'datetime'];

    public function appointment(){ return $this->belongsTo(Appointment::class); }
    public function reviewer(){ return $this->belongsTo(User::class, 'reviewer_id'); }
    public function reviewable(){ return $this->morphTo(); }
}
