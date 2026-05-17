<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningModule extends Model {
    protected $fillable = [
        'subject_name',
        'title',
        'file_path',
        'link_url',
        'type',
        'is_validated',
    ];
}
