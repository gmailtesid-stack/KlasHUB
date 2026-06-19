<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\BelongsToClass;

class Student extends Authenticatable
{
    use Notifiable, BelongsToClass;

    protected $fillable = [
        'class_id',
        'nim',
        'name',
        'password',
        'role',
        'device_id',
        'onesignal_id', // 👈 Mengizinkan field ini diupdate secara massal
    ];

    protected $hidden = [
        'password',
    ];

    public function attendances()
    {
        return $this->hasMany(ClassAttendance::class);
    }

    public function cashLedgers()
    {
        return $this->hasMany(CashLedger::class);
    }
}