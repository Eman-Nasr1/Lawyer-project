<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class AppointmentFile extends Model
{
    use SoftDeletes;

    protected $fillable = ['appointment_id', 'uploader_id', 'name', 'path'];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }


    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }
}
