<?php

namespace App\Modules\VehicleImport\Policies;

use App\Models\User;
use App\Modules\VehicleImport\Models\VehicleImport;

class VehicleImportPolicy
{
    public function view(User $user, VehicleImport $import): bool
    {
        return $user->id === $import->user_id || $user->id === $import->importer_id;
    }

    public function update(User $user, VehicleImport $import): bool
    {
        return $user->id === $import->user_id || $user->id === $import->importer_id;
    }

    public function delete(User $user, VehicleImport $import): bool
    {
        return $user->id === $import->user_id;
    }
}
