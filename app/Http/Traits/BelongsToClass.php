<?php

namespace App\Http\Traits;

use App\Models\Scopes\ClassScope;
use Illuminate\Support\Facades\Auth;

trait BelongsToClass
{
    protected static function bootBelongsToClass()
    {
        static::addGlobalScope(new ClassScope);

        static::creating(function ($model) {
            if (Auth::check() && empty($model->class_id)) {
                $model->class_id = Auth::user()->class_id;
            }
        });
    }
}
