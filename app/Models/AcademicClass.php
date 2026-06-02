<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicClass extends Model
{
    protected $fillable = ['name', 'code', 'academic_year', 'qris_image', 'department', 'contact'];

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function ketuaKelas()
    {
        return $this->hasOne(Student::class, 'class_id')->where('role', 'ketua_kelas');
    }
}
