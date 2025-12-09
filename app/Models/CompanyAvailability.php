<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyAvailability extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id','day_of_week','date','start_time','end_time','is_active'
    ];

    public function company(){ return $this->belongsTo(Company::class); }

    // Scopes
    public function scopeActive($q){ return $q->where('is_active', true); }
    public function scopeForDate($q, string $date){
        $dow = strtolower(date('l', strtotime($date))); // monday..sunday
        return $q->where(function($qq) use ($date, $dow){
            $qq->where('date', $date)->orWhere('day_of_week', $dow);
        });
    }
}
