<?php

namespace App\Modules\VehicleImport\Events;

use App\Modules\VehicleImport\Enums\ImportStep;
use App\Modules\VehicleImport\Models\VehicleImport;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ImportStepCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public VehicleImport $import,
        public ImportStep $step
    ) {
        //
    }
}
