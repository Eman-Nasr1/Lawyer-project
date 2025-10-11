<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Lawyer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'professional_card_image',
        'years_of_experience',
        'bio',
        'avg_rating',
        'reviews_count',
        'is_approved',
        'is_featured',
        'admin_notes',
    ];

    // (اختياري) لو عايزة يرجّع URL البطاقة في JSON
    protected $appends = ['professional_card_image_url'];

    public function getProfessionalCardImageUrlAttribute()
    {
        return $this->professional_card_image
            ? Storage::url($this->professional_card_image)
            : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 👈 العلاقة المطلوبة (Many-to-Many) مع specialties
    public function specialties()
    {
        return $this->belongsToMany(Specialty::class, 'lawyer_specialty');
    }
}
