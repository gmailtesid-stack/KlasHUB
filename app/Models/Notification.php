<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToClass;

class Notification extends Model
{
    use BelongsToClass;

    protected $fillable = [
        'class_id',
        'student_id',
        'message',
        'is_read'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academicClass()
    {
        return $this->belongsTo(AcademicClass::class, 'class_id');
    }
}
