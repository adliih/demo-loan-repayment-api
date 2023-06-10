<?php

namespace Database\Factories;

use App\Enums\RepaymentState;
use App\Models\ScheduledRepayment;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduledRepaymentFactory extends Factory
{
    protected $model = ScheduledRepayment::class;

    public function definition()
    {
        return [
            'loan_id' => function () {
                return \App\Models\Loan::factory()->create()->id;
            },
            'due_at' => $this->faker->date(),
            'currency' => $this->faker->currencyCode,
            'amount' => $this->faker->randomFloat(2, 100, 1000),
            'state' => RepaymentState::PENDING,
        ];
    }
}