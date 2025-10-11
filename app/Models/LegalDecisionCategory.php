<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegalDecisionCategory extends Model
{
    protected $fillable = ['name','slug'];

    public function decisions()
    {
        return $this->hasMany(LegalDecision::class, 'category_id');
    }
}
