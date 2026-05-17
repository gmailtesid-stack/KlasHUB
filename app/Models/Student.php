<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable {
    use Notifiable;

    protected $fillable = [
        'nim',
        'name',
        'password',
        'role',
        'device_id',
    ];

    protected $hidden = [
        'password',
    ];

    public function attendances() {
        return $this->hasMany(ClassAttendance::class);
    }

    public function cashLedgers() {
        return $this->hasMany(CashLedger::class);
    }
}
