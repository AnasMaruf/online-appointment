<?php

namespace App\Models\Appointment;

use App\Models\Host\Prequestion;
use Illuminate\Database\Eloquent\Model;

class PrequestionAnswer extends Model
{
    protected $fillable = [
        'uuid',
        'appointment_id',
        'question_id',
        'answer',
    ];

    public function question()
    {
        return $this->belongsTo(Prequestion::class);
    }
}
