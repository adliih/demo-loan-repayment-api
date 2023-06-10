<?php

namespace App\Services;

use App\Enums\LoanState;
use App\Enums\RepaymentState;
use App\Models\Loan;
use App\Models\ScheduledRepayment;
use Illuminate\Support\Facades\Auth;

class LoanService
{
  public function submitLoan(array $data, $userId): Loan
  {
    $loan = new Loan();
    $loan->currency = $data['currency'];
    $loan->amount = $data['amount'];
    $loan->term = $data['term'];
    $loan->submitted_at = $data['submitted_at'];
    $loan->state = LoanState::PENDING;
    $loan->user_id = $userId;
    $loan->save();

    $loan->scheduled_repayments = collect();

    // Generate scheduled repayments
    $repaymentAmount = round($loan->amount / $loan->term, 2);
    $parity = $loan->amount - $repaymentAmount * $loan->term;

    for ($i = 1; $i <= $loan->term; $i++) {
      $repayment = new ScheduledRepayment();
      $repayment->loan_id = $loan->id;
      $repayment->due_at = date('Y-m-d', strtotime($loan->submitted_at . " + $i week"));
      $repayment->currency = $loan->currency;
      $repayment->amount = $repaymentAmount;
      $repayment->state = RepaymentState::PENDING;

      // apply parity at the end of term
      if ($i === $loan->term) {
        $repayment->amount += $parity;
      }
      $repayment->save();

      $loan->scheduled_repayments->add($repayment);
    }

    return $loan;
  }

  public function approveLoan(Loan $loan): Loan
  {
    $loan->state = LoanState::APPROVED;
    $loan->approved_by_id = Auth::id();
    $loan->save();

    return $loan;
  }

  public function getCustomerLoans($userId): array
  {
    $loans = Loan::with('scheduled_repayments.repayment')->where('user_id', $userId)->get();

    return $loans->toArray();
  }
}