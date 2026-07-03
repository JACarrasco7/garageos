<?php

namespace Database\Factories\Modules\Billing\Models;

use App\Modules\Billing\Models\PaymentIntent;
use App\Modules\Billing\Models\PlatformFee;
use App\Modules\Billing\Models\StripeAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlatformFeeFactory extends Factory
{
    protected $model = PlatformFee::class;

    public function definition(): array
    {
        return [
            'payment_intent_id' => PaymentIntent::factory(),
            'stripe_account_id' => StripeAccount::factory(),
            'amount' => $this->faker->randomFloat(2, 50, 500),
            'currency' => 'EUR',
            'description' => $this->faker->sentence(),
            'metadata' => null,
            'processed_at' => now(),
            'invoice_path' => null,
        ];
    }
}
