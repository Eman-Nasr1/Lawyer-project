<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'addressable_type',
        'addressable_id',

        'title',
        'building_number',
        'apartment_number',
        'floor_number',
        'type',          // home|work|headquarter|branch|office
        'city',
        'address_line',
        'lat',
        'lng',
        'is_primary',
    ];

    protected $casts = [
        'lat'        => 'decimal:7',
        'lng'        => 'decimal:7',
        'is_primary' => 'boolean',
    ];

    public function addressable()
    {
        return $this->morphTo();
    }

    /* Scopes */
    public function scopePrimary($q)   { return $q->where('is_primary', true); }
    public function scopeOfType($q,$t){ return $q->where('type', $t); }
}
