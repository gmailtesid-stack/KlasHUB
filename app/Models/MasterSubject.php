<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterSubject extends Model
{
    protected $fillable = ['class_id', 'name', 'sks', 'code', 'default_lecturer'];
}
