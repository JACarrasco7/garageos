<?php

namespace Database\Factories\Modules\Billing\Models;

use App\Models\User;
use App\Modules\Billing\Models\StripeAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class StripeAccountFactory extends Factory
{
    protected $model = StripeAccount::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'stripe_account_id' => 'acct_'.$this->faker->regexify('[a-zA-Z0-9]{16}'),
            'charges_enabled' => true,
            'payouts_enabled' => true,
            'country' => 'ES',
            'business_type' => 'individual',
            'business_profile' => null,
            'onboarding_completed' => true,
            'tos_acceptance_date' => now(),
        ];
    }
}
