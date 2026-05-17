<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashLedger extends Model {
    protected $fillable = [
        'student_id',
        'type',
        'amount',
        'description',
        'transaction_date',
        'is_validated',
    ];

    protected $casts = [
        'transaction_date' => 'date',
    ];

    public function student() {
        return $this->belongsTo(Student::class);
    }
}
