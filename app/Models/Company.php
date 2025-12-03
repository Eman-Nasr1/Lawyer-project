<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'professional_card_image',
        'years_of_experience',
        'description',
        'avg_rating',
        'reviews_count',
        'is_approved',
        'is_featured',
    ];

    /* Relationships */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // app/Models/Company.php

    public function lawyers()
    {
        return $this->belongsToMany(\App\Models\Lawyer::class, 'company_lawyer')
            ->withPivot('title', 'is_primary')
            ->withTimestamps();
    }

    // 👈 العلاقة المطلوبة (Many-to-Many) مع specialties
    public function specialties()
    {
        return $this->belongsToMany(Specialty::class, 'company_specialty');
    }
    protected $appends = ['professional_card_image_url'];

    public function getProfessionalCardImageUrlAttribute()
    {
        if (!$this->professional_card_image) {
            return null;
        }

        return Storage::disk('public')->url($this->professional_card_image);
    }

    // عناوين الشركة (polymorphic)
    public function addresses()
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function primaryAddress()
    {
        return $this->morphOne(Address::class, 'addressable')->where('is_primary', true);
    }

    /* Scopes */
    public function scopeApproved($q)
    {
        return $q->where('is_approved', true);
    }
    public function scopeFeatured($q)
    {
        return $q->where('is_featured', true);
    }

    /* Helpers */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }
    // app/Models/Company.php
    public function favorites()
    {
        return $this->morphMany(\App\Models\Favorite::class, 'favoritable');
    }
    public function reviews()
    {
        return $this->morphMany(\App\Models\Review::class, 'reviewable');
    }
}
