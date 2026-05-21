<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToClass;

class LearningModule extends Model
{
    use BelongsToClass;

    protected $fillable = [
        'class_id',
        'subject_name',
        'title',
        'file_path',
        'file_content',
        'mime_type',
        'link_url',
        'type',
        'is_validated',
    ];

    // Don't return file_content in JSON by default (too large)
    protected $hidden = ['file_content'];
}
