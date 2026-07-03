<?php

namespace App\Policies;

use App\Models\User;
use App\Modules\Providers\Models\Provider;

class ProviderPolicy
{
    public function view(User $user, Provider $provider): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasVerifiedEmail();
    }

    public function update(User $user, Provider $provider): bool
    {
        return $provider->user_id === $user->id;
    }

    public function delete(User $user, Provider $provider): bool
    {
        return $provider->user_id === $user->id;
    }
}
