<?php

namespace Database\Factories;

use App\Enums\RepaymentState;
use App\Models\Repayment;
use App\Models\ScheduledRepayment;
use Illuminate\Database\Eloquent\Factories\Factory;

class RepaymentFactory extends Factory
{
    protected $model = Repayment::class;

    public function definition()
    {
        return [
            'loan_id' => function () {
                return \App\Models\Loan::factory()->create()->id;
            },
            'scheduled_repayment_id' => function () {
                return ScheduledRepayment::factory()->create()->id;
            },
            'currency' => $this->faker->currencyCode,
            'amount' => $this->faker->randomFloat(2, 100, 1000),
        ];
    }
}