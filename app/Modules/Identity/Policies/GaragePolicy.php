<?php

namespace App\Modules\Identity\Policies;

use App\Models\User;
use App\Modules\Identity\Models\Garage;

class GaragePolicy
{
    public function view(User $user, Garage $garage): bool
    {
        return $garage->user_id === $user->id;
    }

    public function update(User $user, Garage $garage): bool
    {
        return $garage->user_id === $user->id;
    }

    public function delete(User $user, Garage $garage): bool
    {
        return $garage->user_id === $user->id;
    }
}
