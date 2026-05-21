<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToClass;

class Assignment extends Model
{
    use BelongsToClass;

    protected $fillable = [
        'class_id',
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
