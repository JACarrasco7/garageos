<?php

namespace App\Policies;

use App\Models\User;
use App\Modules\VehicleImport\Models\VehicleImport;

class VehicleImportPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, VehicleImport $vehicleImport): bool
    {
        return $vehicleImport->user_id === $user->id || $vehicleImport->importer_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, VehicleImport $vehicleImport): bool
    {
        return $vehicleImport->user_id === $user->id || $vehicleImport->importer_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, VehicleImport $vehicleImport): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, VehicleImport $vehicleImport): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, VehicleImport $vehicleImport): bool
    {
        return false;
    }
}
