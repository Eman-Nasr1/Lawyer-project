<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppointmentFile extends Model
{
    use SoftDeletes;

    protected $fillable = ['appointment_id','uploader_id','name','path'];

    public function appointment() { return $this->belongsTo(Appointment::class); }
    public function uploader()    { return $this->belongsTo(User::class, 'uploader_id'); }

    // رابط الملف
    public function getUrlAttribute(): string
    {
        return asset('storage/'.$this->path);
    }
}
