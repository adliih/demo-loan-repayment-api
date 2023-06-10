<?php

namespace Tests\Unit\Services;

use App\Enums\LoanState;
use App\Enums\RepaymentState;
use App\Models\Loan;
use App\Models\Repayment;
use App\Models\ScheduledRepayment;
use App\Services\RepaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class RepaymentServiceTest extends TestCase
{
  use RefreshDatabase;

  /** @var RepaymentService */
  private $repaymentService;

  protected function setUp(): void
  {
    parent::setUp();

    $this->repaymentService = new RepaymentService();
  }

  public function testSubmitRepayment(): void
  {
    $loan = Loan::factory()->create(['state' => LoanState::APPROVED]);

    $scheduledRepayment = ScheduledRepayment::factory()->create([
      'loan_id' => $loan->id,
      'state' => RepaymentState::PENDING,
    ]);

    $repaymentData = [
      'currency' => 'USD',
      'amount' => 10000,
      'scheduled_repayment_id' => $scheduledRepayment->id,
    ];

    $repayment = $this->repaymentService->submitRepayment($repaymentData, $loan->id);

    $this->assertInstanceOf(Repayment::class, $repayment);
    $this->assertEquals($repaymentData['currency'], $repayment->currency);
    $this->assertEquals($repaymentData['amount'], $repayment->amount);
    $this->assertEquals($repaymentData['scheduled_repayment_id'], $repayment->scheduled_repayment_id);

    $scheduledRepayment->refresh();
    $this->assertEquals(RepaymentState::PAID, $scheduledRepayment->state);

    $loan->refresh();
    $this->assertEquals(LoanState::PAID, $loan->state);
  }

  public function testSubmitRepaymentHasExcessPayment(): void
  {
    $loan = Loan::factory()->create(['state' => LoanState::APPROVED, 'amount' => 10000, 'term' => 3]);

    $scheduledRepayments = [];
    // first scheduled repayment is paid with more than the amount
    $scheduledRepayments[] = ScheduledRepayment::factory()->create([
      'loan_id' => $loan->id,
      'state' => RepaymentState::PAID,
      'amount' => 3333,
    ]);
    Repayment::factory()->create([
      'amount' => 5000,
      'loan_id' => $loan->id,
      'scheduled_repayment_id' => $scheduledRepayments[0]->id,
    ]);
    // second scheduled repayment is pending
    $scheduledRepayments[] = ScheduledRepayment::factory()->create([
      'loan_id' => $loan->id,
      'state' => RepaymentState::PENDING,
      'amount' => 3333,
    ]);
    // third scheduled repayment is pending
    $scheduledRepayments[] = ScheduledRepayment::factory()->create([
      'loan_id' => $loan->id,
      'state' => RepaymentState::PENDING,
      'amount' => 3334,
    ]);

    // want to pay the second repayment
    $scheduledRepayment = $scheduledRepayments[1];

    $repaymentData = [
      'currency' => 'USD',
      'amount' => 1666,
      // since we already paid 5000 before, we should only need to pay 1666
      'scheduled_repayment_id' => $scheduledRepayment->id,
    ];

    $repayment = $this->repaymentService->submitRepayment($repaymentData, $loan->id);

    $this->assertInstanceOf(Repayment::class, $repayment);
  }

  public function testSubmitRepaymentBelowRequiredAmount(): void
  {
    $loan = Loan::factory()->create(['state' => LoanState::APPROVED, 'amount' => 10000, 'term' => 3]);

    $scheduledRepayments = [];
    $scheduledRepayments[] = ScheduledRepayment::factory()->create([
      'loan_id' => $loan->id,
      'state' => RepaymentState::PENDING,
      'amount' => 3333,
    ]);
    $scheduledRepayments[] = ScheduledRepayment::factory()->create([
      'loan_id' => $loan->id,
      'state' => RepaymentState::PENDING,
      'amount' => 3333,
    ]);
    $scheduledRepayments[] = ScheduledRepayment::factory()->create([
      'loan_id' => $loan->id,
      'state' => RepaymentState::PENDING,
      'amount' => 3334,
    ]);

    $scheduledRepayment = $scheduledRepayments[0];

    $repaymentData = [
      'currency' => 'USD',
      'amount' => 3000,
      'scheduled_repayment_id' => $scheduledRepayment->id,
    ];

    $this->expectException(BadRequestHttpException::class);

    $this->repaymentService->submitRepayment($repaymentData, $loan->id);

    $this->assertTrue(false);
  }
}