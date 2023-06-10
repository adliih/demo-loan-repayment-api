<?php

namespace Tests\Feature\Controller;

use App\Models\Loan;
use App\Models\User;
use Tests\TestCase;

class LoanControllerTest extends TestCase
{
  function testIndexShouldShowOwnedLoansOnly()
  {
    $user = User::factory()->customer()->create();
    $anotherUser = User::factory()->customer()->create();

    Loan::factory()->create([
      'user_id' => $anotherUser,
    ]);

    $response = $this->actingAs($user)->getJson(route('loans.index'));


    $response->assertExactJson(['loans' => []]);
  }
  function testApproveByAdminShouldBeAllowed()
  {
    $customer = User::factory()->customer()->create();
    $admin = User::factory()->admin()->create();

    $loan = Loan::factory()->create(['user_id' => $customer]);

    $response = $this->actingAs($admin)->postJson(route('loans.approve', ['id' => $loan->id]));

    $response->assertOk();
  }
  function testApproveByCustomerShouldNotBeAllowed()
  {
    $user = User::factory()->customer()->create();

    $response = $this->actingAs($user)->postJson(route('loans.approve', ['id' => 1]));

    $response->assertForbidden();
  }
}