<?php

namespace App\Modules\VehicleImport\Jobs;

use App\Modules\VehicleImport\Models\TemporaryPlate;
use App\Notifications\TemporaryPlateExpiringNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPlateExpiryReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public TemporaryPlate $plate)
    {
        $this->onQueue('high');
    }

    public function handle(): void
    {
        TemporaryPlateExpiringNotification::send(
            $this->plate->vehicleImport->user,
            $this->plate
        );
    }
}
