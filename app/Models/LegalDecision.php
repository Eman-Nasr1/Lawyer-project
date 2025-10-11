<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegalDecision extends Model
{
    protected $fillable = ['category_id','title','body','source_url','published_at'];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(LegalDecisionCategory::class, 'category_id');
    }
}
