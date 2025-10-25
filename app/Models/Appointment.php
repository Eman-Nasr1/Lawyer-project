<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'lawyer_id',
        'company_id',
        'date',
        'start_time',
        'end_time',
        'status',
        'notes',
        'attachments',
    ];

    /*-----------------------------
    | 🔹 العلاقات (Relationships)
    ------------------------------*/

    // العميل (User)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // المحامي
    public function lawyer()
    {
        return $this->belongsTo(Lawyer::class);
    }

    // الشركة (اختياري)
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /*-----------------------------
    | 🔹 سكوبات مفيدة (Query Scopes)
    ------------------------------*/

    // المواعيد المؤكدة فقط
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    // المواعيد قيد الانتظار
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // المواعيد المكتملة
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // حسب تاريخ معين
    public function scopeForDate($query, string $date)
    {
        return $query->whereDate('date', $date);
    }

    /*-----------------------------
    | 🔹 Accessors / Helpers
    ------------------------------*/

    // دمج التاريخ والوقت لتاريخ ووقت كامل
    public function getStartDateTimeAttribute(): string
    {
        return "{$this->date} {$this->start_time}";
    }

    public function getEndDateTimeAttribute(): string
    {
        return "{$this->date} {$this->end_time}";
    }
    public function files()
    {
        return $this->hasMany(AppointmentFile::class);
    }
    public function cancellations()
    {
        return $this->hasMany(AppointmentCancellation::class);
    }
}
