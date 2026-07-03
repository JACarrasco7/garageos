<?php

namespace App\Policies;

use App\Models\User;
use App\Modules\Billing\Models\StripeAccount;

class StripeAccountPolicy
{
    public function view(User $user, StripeAccount $account): bool
    {
        return $account->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasVerifiedEmail();
    }

    public function update(User $user, StripeAccount $account): bool
    {
        return $account->user_id === $user->id;
    }

    public function delete(User $user, StripeAccount $account): bool
    {
        return $account->user_id === $user->id;
    }
}
