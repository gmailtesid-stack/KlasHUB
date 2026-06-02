<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToClass;

class CashLedger extends Model
{
    use BelongsToClass;

    protected $fillable = [
        'class_id',
        'student_id',
        'type',
        'amount',
        'description',
        'transaction_date',
        'is_validated',
        'proof_image',
    ];

    protected $casts = [
        'transaction_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
