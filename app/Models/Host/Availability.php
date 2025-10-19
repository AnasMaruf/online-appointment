<?php

namespace App\Models\Host;

use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    protected $fillable = [
        'uuid',
        'host_id',
        'day',
        'time_start',
        'time_end',
    ];
}
