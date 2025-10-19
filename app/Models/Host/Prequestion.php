<?php

namespace App\Models\Host;

use Illuminate\Database\Eloquent\Model;

class Prequestion extends Model
{
    protected $fillable = [
        'uuid',
        'host_id',
        'question',
    ];
}
