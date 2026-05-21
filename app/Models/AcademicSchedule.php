<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToClass;

class AcademicSchedule extends Model
{
    use BelongsToClass;

    protected $fillable = [
        'class_id',
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
