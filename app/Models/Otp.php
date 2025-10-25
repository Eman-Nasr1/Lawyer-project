<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Otp extends Model
{
    protected $fillable = [
        'user_id','channel','purpose','code_hash','expires_at',
        'consumed_at','attempts','otp_token','otp_token_expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'consumed_at' => 'datetime',
        'otp_token_expires_at' => 'datetime',
    ];

    public function isExpired(): bool    { return now()->greaterThan($this->expires_at); }
    public function isConsumed(): bool   { return !is_null($this->consumed_at); }
    public function tokenExpired(): bool { return $this->otp_token_expires_at && now()->greaterThan($this->otp_token_expires_at); }

    public function user(){ return $this->belongsTo(User::class); }
}
