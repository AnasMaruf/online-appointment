<?php

namespace App\Models\Appointment;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'uuid',
        'host_id',
        'name',
        'email',
        'phone_number',
        'date',
        'time_start',
        'time_end',
        'note',
        'status', // (pending_approval, upcoming, rejected, canceled)
    ];
}
