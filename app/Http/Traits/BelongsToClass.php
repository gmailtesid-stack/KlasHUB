<?php

namespace App\Http\Traits;

use App\Models\Scopes\ClassScope;
use App\Models\Scopes\SemesterScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait BelongsToClass
{
    protected static function bootBelongsToClass()
    {
        static::addGlobalScope(new ClassScope);

        $className = class_basename(get_called_class());
        if (
            in_array($className, [
                'AcademicSchedule',
                'Assignment',
                'CashLedger',
                'ClassAttendance',
                'LearningModule',
                'MasterSubject'
            ])
        ) {
            static::addGlobalScope(new SemesterScope);
        }

        static::creating(function ($model) use ($className) {
            if (Auth::check()) {
                if (empty($model->class_id)) {
                    $model->class_id = Auth::user()->class_id;
                }

                if (
                    in_array($className, [
                        'AcademicSchedule',
                        'Assignment',
                        'CashLedger',
                        'ClassAttendance',
                        'LearningModule',
                        'MasterSubject'
                    ])
                ) {
                    if (empty($model->semester)) {
                        $activeSemester = DB::table('academic_classes')
                            ->where('id', Auth::user()->class_id)
                            ->value('semester_ke');
                        $model->semester = $activeSemester ?? 1;
                    }
                }
            }
        });
    }
}
