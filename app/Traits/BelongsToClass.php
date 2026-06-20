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

            // Set default semester for relevant models
            if (Auth::check()) {
                $className = class_basename($model);
                if (in_array($className, ['AcademicSchedule', 'Assignment', 'CashLedger', 'ClassAttendance', 'LearningModule', 'MasterSubject'])) {
                    if (empty($model->semester)) {
                        $activeSemester = \Illuminate\Support\Facades\DB::table('academic_classes')
                            ->where('id', Auth::user()->class_id)
                            ->value('semester_ke');
                        $model->semester = $activeSemester ?? 1;
                    }
                }
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

        static::addGlobalScope('semester_isolation', function (Builder $builder) {
            // Apply SemesterScope for relevant models
            $model = $builder->getModel();
            $className = class_basename($model);
            if (Auth::hasUser() && Auth::user()->role !== 'super_admin' && in_array($className, ['AcademicSchedule', 'Assignment', 'CashLedger', 'ClassAttendance', 'LearningModule', 'MasterSubject'])) {
                $requestSemester = request()->get('semester');
                if ($requestSemester) {
                    $builder->where($builder->getQuery()->from . '.semester', $requestSemester);
                } else {
                    $activeSemester = \Illuminate\Support\Facades\DB::table('academic_classes')
                        ->where('id', Auth::user()->class_id)
                        ->value('semester_ke');
                    $builder->where($builder->getQuery()->from . '.semester', $activeSemester ?? 1);
                }
            }
        });
    }

    public function academicClass()
    {
        return $this->belongsTo(AcademicClass::class, 'class_id');
    }
}
