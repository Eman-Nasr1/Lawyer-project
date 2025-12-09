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
        if (!$this->professional_card_image) {
            return null;
        }

        return Storage::disk('public')->url($this->professional_card_image);
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
    public function addresses()
    {
        return $this->morphMany(Address::class, 'addressable');
    }
    public function primaryAddress()
    {
        return $this->morphOne(Address::class, 'addressable')->where('is_primary', true);
    }
    public function favorites()
    {
        return $this->morphMany(\App\Models\Favorite::class, 'favoritable');
    }
    public function reviews()
    {
        return $this->morphMany(\App\Models\Review::class, 'reviewable');
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class, 'company_id');
    }

    public function companies()
    {
        return $this->belongsToMany(\App\Models\Company::class, 'company_lawyer')
            ->withPivot(['title', 'is_primary'])
            ->withTimestamps();
    }
    
    public function availabilities()
    {
        return $this->hasMany(LawyerAvailability::class);
    }
    
    /**
     * Check if lawyer profile is complete
     * @return array ['is_complete' => bool, 'missing_fields' => array]
     */
    public function isProfileComplete(): array
    {
        $missingFields = [];
        
        // Check for primary address
        if (!$this->primaryAddress) {
            $missingFields[] = 'address';
        }
        
        // Check for at least one availability slot
        if (!$this->availabilities()->where('is_active', true)->exists()) {
            $missingFields[] = 'availability';
        }
        
        // Check for professional card image
        if (!$this->professional_card_image) {
            $missingFields[] = 'professional_card_image';
        }
        
        // Optional: Check for specialties
        // if ($this->specialties()->count() === 0) {
        //     $missingFields[] = 'specialties';
        // }
        
        return [
            'is_complete' => empty($missingFields),
            'missing_fields' => $missingFields,
        ];
    }
}
