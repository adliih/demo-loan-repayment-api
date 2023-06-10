<?php

namespace App\Models;

use App\Enums\RepaymentState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledRepayment extends Model
{
    use HasFactory;
    protected $fillable = [
        'loan_id',
        'due_at',
        'currency',
        'amount',
        'state',
    ];

    protected $casts = [
        'state' => RepaymentState::class,
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function repayment()
    {
        return $this->hasOne(Repayment::class);
    }
}