<?php

namespace Database\Factories;

use App\Models\Loan;
use App\Enums\LoanState;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
  protected $model = Loan::class;

  public function definition()
  {
    return [
      'currency' => $this->faker->randomElement(['USD']),
      'amount' => $this->faker->numberBetween(1000, 10000),
      'term' => $this->faker->numberBetween(1, 12),
      'submitted_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
      'state' => LoanState::PENDING,
      'user_id' => User::factory()->create(), // Update with appropriate user ID
    ];
  }
}