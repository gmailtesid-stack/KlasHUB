<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningModule extends Model {
    protected $fillable = [
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
