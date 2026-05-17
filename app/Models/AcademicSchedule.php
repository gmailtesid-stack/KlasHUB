<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicSchedule extends Model {
    protected $fillable = [
        'subject_name',
        'subject_code',
        'lecturer_name',
        'day',
        'time_start',
        'time_end',
        'room',
        'class_name',
        'delivery_type',
        'is_validated',
    ];
}
