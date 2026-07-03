<?php

namespace App\Modules\Vehicle\Events;

use App\Modules\Vehicle\Models\Vehicle;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VehicleRegistered
{
    use Dispatchable, SerializesModels;

    public function __construct(public Vehicle $vehicle) {}
}
