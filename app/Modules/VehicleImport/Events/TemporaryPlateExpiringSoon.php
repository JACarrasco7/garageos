<?php

namespace App\Modules\VehicleImport\Events;

use App\Modules\VehicleImport\Models\TemporaryPlate;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TemporaryPlateExpiringSoon
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public TemporaryPlate $plate
    ) {
        //
    }
}