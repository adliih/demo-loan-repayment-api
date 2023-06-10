<?php

namespace App\Models;

use App\Enums\RepaymentState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repayment extends Model
{

    use HasFactory;
    protected $fillable = [
        'loan_id',
        'scheduled_repayment_id',
        'currency',
        'amount',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function scheduled_repayment()
    {
        return $this->belongsTo(ScheduledRepayment::class);
    }
}