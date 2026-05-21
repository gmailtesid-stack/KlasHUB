<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToClass;

class ClassAttendance extends Model
{
    use BelongsToClass;

    protected $fillable = [
        'class_id',
        'student_id',
        'subject_name',
        'attendance_date',
        'status',
        'is_validated',
    ];

    protected $casts = [
        'attendance_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
