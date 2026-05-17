<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model {
    protected $fillable = [
        'subject_name',
        'title',
        'description',
        'deadline',
        'material_link',
        'type',
        'members',
        'is_validated',
    ];
    
    protected $casts = [
        'deadline' => 'datetime',
    ];
}
