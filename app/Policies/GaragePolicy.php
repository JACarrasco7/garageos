<?php

namespace App\Policies;

use App\Models\User;
use App\Modules\Identity\Models\Garage;

class GaragePolicy
{
    public function view(User $user, Garage $garage): bool
    {
        return $user->id === $garage->user_id;
    }

    public function update(User $user, Garage $garage): bool
    {
        return $user->id === $garage->user_id;
    }
}
