<?php

namespace App\Models;

use App\Enums\LoanState;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'currency',
        'amount',
        'term',
        'submitted_at',
        'state',
        'user_id',
        'approved_by_id',
    ];

    protected $casts = [
        'state' => LoanState::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scheduled_repayments()
    {
        return $this->hasMany(ScheduledRepayment::class);
    }
}