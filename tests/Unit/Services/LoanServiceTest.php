<?php

namespace Tests\Unit\Services;

use App\Enums\LoanState;
use App\Enums\RepaymentState;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\LoanService;
use App\Models\Loan;
use App\Models\User;
use Tests\TestCase;

class LoanServiceTest extends TestCase
{
  use RefreshDatabase;

  /** @var LoanService */
  private $loanService;

  protected function setUp(): void
  {
    parent::setUp();

    $this->loanService = new LoanService();
  }

  public function testSubmitLoan()
  {
    $data = [
      'currency' => 'USD',
      'amount' => 10000,
      'term' => 3,
      'submitted_at' => '2022-02-07',
    ];
    $userId = User::factory()->create()->id;

    $loan = $this->loanService->submitLoan($data, $userId);

    $this->assertInstanceOf(Loan::class, $loan);
    $this->assertEquals(LoanState::PENDING, $loan->state);
    $this->assertCount(3, $loan->scheduled_repayments);

    foreach ($loan->scheduled_repayments as $repayment) {
      $this->assertEquals(RepaymentState::PENDING, $repayment->state);
    }

    // total scheduled repayment amount should be same with loan amount
    $this->assertEquals(
      collect($loan->scheduled_repayments)->sum('amount'),
      $loan->amount
    );
  }

  public function testApproveLoan()
  {
    $loan = Loan::factory()->create();

    $approvedLoan = $this->loanService->approveLoan($loan);

    $this->assertInstanceOf(Loan::class, $approvedLoan);
    $this->assertEquals(LoanState::APPROVED, $approvedLoan->state);
  }

  public function testGetCustomerLoans()
  {
    $userId = User::factory()->create()->id;

    Loan::factory()->count(2)->create(['user_id' => $userId]);

    $loans = $this->loanService->getCustomerLoans($userId);

    $this->assertIsArray($loans);
    $this->assertCount(2, $loans);
  }
}