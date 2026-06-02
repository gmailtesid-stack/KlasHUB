<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SemesterScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (Auth::check() && Auth::user()->role !== 'super_admin') {
            $requestSemester = request()->get('semester');
            if ($requestSemester) {
                // If a specific semester is requested (Arsip/History)
                $builder->where($model->getTable() . '.semester', $requestSemester);
            } else {
                // Otherwise, only load the active semester of the class
                $activeSemester = DB::table('academic_classes')
                    ->where('id', Auth::user()->class_id)
                    ->value('semester_ke');
                $builder->where($model->getTable() . '.semester', $activeSemester ?? 1);
            }
        }
    }
}
