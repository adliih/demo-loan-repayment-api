<?php

namespace App\Services;

use App\Enums\LoanState;
use App\Enums\RepaymentState;
use App\Models\Repayment;
use App\Models\Loan;
use App\Models\ScheduledRepayment;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class RepaymentService
{
  public function submitRepayment(array $data, $loanId): Repayment
  {
    $userId = Auth::id();
    $loan = Loan::with('scheduled_repayments.repayment')->where('user_id', $userId)->findOrFail($loanId);
    $scheduledRepayment = $loan->scheduled_repayments->where('id', $data['scheduled_repayment_id'])->firstOrFail();

    // calculate minimum required amount
    $excessFromPreviousPayment = $loan->scheduled_repayments
      ->takeUntil($scheduledRepayment)
      ->sum(function (ScheduledRepayment $item) {
        $repaymentAmount = $item->repayment->amount ?? 0;
        $scheduledRepaymentAmount = $item->amount ?? 0;

        return $repaymentAmount - $scheduledRepaymentAmount;
      });

    $requiredRepaymentAmount = $scheduledRepayment->amount - $excessFromPreviousPayment;

    if ($data['amount'] < $requiredRepaymentAmount) {
      throw new BadRequestHttpException('Insufficient repayment amount');
    }

    $repayment = new Repayment();
    $repayment->loan_id = $loanId;
    $repayment->scheduled_repayment_id = $data['scheduled_repayment_id'];
    $repayment->currency = $data['currency'];
    $repayment->amount = $data['amount'];
    $repayment->save();

    // Update scheduled repayment status
    $scheduledRepayment->state = RepaymentState::PAID;
    $scheduledRepayment->save();

    // Update loan status if all scheduled repayments are paid
    $allRepaymentsPaid = $loan->scheduled_repayments()->where('state', '!=', RepaymentState::PAID)->doesntExist();

    if ($allRepaymentsPaid) {
      $loan->state = LoanState::PAID;
      $loan->save();
    }

    return $repayment;
  }
}