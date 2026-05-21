<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicClass extends Model
{
    protected $fillable = ['name', 'code', 'academic_year'];

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }
}
