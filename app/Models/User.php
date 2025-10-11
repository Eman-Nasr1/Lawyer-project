<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;           // ⬅️ مهم
use Spatie\Permission\Traits\HasRoles;       // اختياري لو بتستخدمي Spatie
use Illuminate\Support\Facades\Storage;       // لو عاملة accessor للصور

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;  // ⬅️ مهم
    protected $guard_name = 'sanctum';
    protected $fillable = [
        'name','email','password','type','avatar','phone',
    ];

    protected $hidden = ['password','remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // (اختياري) لو كنتِ عاملة Accessor للصورة
    protected $appends = ['avatar_url'];
    public function getAvatarUrlAttribute() {
        return $this->avatar ? Storage::url($this->avatar) : null;
    }

    public function lawyer()
    {
        return $this->hasOne(\App\Models\Lawyer::class, 'user_id');
    }
}
