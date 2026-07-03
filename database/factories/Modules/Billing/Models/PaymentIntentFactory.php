<?php

namespace Database\Factories\Modules\Billing\Models;

use App\Models\User;
use App\Modules\Billing\Models\PaymentIntent;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentIntentFactory extends Factory
{
    protected $model = PaymentIntent::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'stripe_payment_intent_id' => 'pi_'.$this->faker->regexify('[a-zA-Z0-9]{16}'),
            'amount' => $this->faker->randomFloat(2, 100, 1000),
            'currency' => 'EUR',
            'status' => 'succeeded',
            'description' => $this->faker->sentence(),
            'metadata' => null,
            'platform_fee_amount' => $this->faker->optional()->randomFloat(2, 10, 100),
            'platform_fee_percent' => 8.00,
            'connected_account_id' => null,
            'application_fee_amount' => 0,
        ];
    }
}
