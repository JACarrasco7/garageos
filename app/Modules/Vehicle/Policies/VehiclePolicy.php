<?php

namespace App\Modules\Vehicle\Policies;

use App\Models\User;
use App\Modules\Vehicle\Models\Vehicle;
use Illuminate\Auth\Access\HandlesAuthorization;

class VehiclePolicy
{
    use HandlesAuthorization;

    public function view(User $user, Vehicle $vehicle): bool
    {
        return $vehicle->garage->user_id === $user->id;
    }

    public function update(User $user, Vehicle $vehicle): bool
    {
        return $vehicle->garage->user_id === $user->id;
    }

    public function delete(User $user, Vehicle $vehicle): bool
    {
        return $vehicle->garage->user_id === $user->id;
    }
}