<?php

namespace App\Models\Host;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $fillable = [
        'uuid',
        'host_id',
        'start_date',
        'end_date',
    ];
}
