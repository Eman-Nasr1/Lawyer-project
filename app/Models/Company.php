<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'user_id',
        'slug',
        'logo',
        // 'city', 'address', 'lat', 'lng',  // تم نقلهم لجدول addresses
        'phone',
        'email',
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

    public function lawyers()
    {
        return $this->hasMany(Lawyer::class);
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
    public function scopeApproved($q)  { return $q->where('is_approved', true); }
    public function scopeFeatured($q)  { return $q->where('is_featured', true); }

    /* Helpers */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? asset('storage/'.$this->logo) : null;
    }
    // app/Models/Company.php
public function favorites() { return $this->morphMany(\App\Models\Favorite::class, 'favoritable'); }
public function reviews()   { return $this->morphMany(\App\Models\Review::class, 'reviewable'); }

}
