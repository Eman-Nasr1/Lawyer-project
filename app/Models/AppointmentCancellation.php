<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppointmentCancellation extends Model
{
    use SoftDeletes;

    protected $fillable = ['appointment_id','cancelled_by','reason','cancelled_at'];

    public function appointment(){ return $this->belongsTo(Appointment::class); }
    public function canceller()  { return $this->belongsTo(User::class, 'cancelled_by'); }
}
