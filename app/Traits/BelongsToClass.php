<?php

namespace App\Traits;

use App\Models\AcademicClass;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToClass
{
    protected static function bootBelongsToClass()
    {
        static::creating(function ($model) {
            if (Auth::check() && !$model->class_id) {
                $model->class_id = Auth::user()->class_id;
            }
        });

        static::addGlobalScope('class_isolation', function (Builder $builder) {
            // Avoid recursion: Only apply scope if Auth is already resolved 
            // and we are not in the middle of resolving it.
            if (Auth::hasUser()) {
                $user = Auth::user();
                if ($user->role !== 'super_admin') {
                    $builder->where($builder->getQuery()->from . '.class_id', $user->class_id);
                }
            }
        });
    }

    public function academicClass()
    {
        return $this->belongsTo(AcademicClass::class, 'class_id');
    }
}
