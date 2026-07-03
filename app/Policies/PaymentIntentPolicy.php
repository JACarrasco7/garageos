<?php

namespace App\Policies;

use App\Models\User;
use App\Modules\Billing\Models\PaymentIntent;

class PaymentIntentPolicy
{
    public function view(User $user, PaymentIntent $intent): bool
    {
        return $intent->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasVerifiedEmail();
    }
}
