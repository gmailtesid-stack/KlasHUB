<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToClass;

class MasterSubject extends Model
{
    use BelongsToClass;

    protected $fillable = ['class_id', 'name', 'sks', 'code', 'default_lecturer'];
}
