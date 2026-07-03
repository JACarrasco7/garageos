<?php

namespace App\Console\Commands;

use App\Modules\VehicleImport\Jobs\CheckTemporaryPlateExpiryJob;
use App\Modules\VehicleImport\Models\TemporaryPlate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class CheckTemporaryPlateExpiryCommand extends Command
{
    protected $signature = 'import:check-plates-expiry';

    protected $description = 'Check for temporary plates expiring soon and send notifications';

    public function handle(): int
    {
        $plates = TemporaryPlate::expiringSoon(7)
            ->whereDoesntHave('vehicleImport', fn ($q) => $q->where('status', 'completed'))
            ->get();

        if ($plates->isEmpty()) {
            $this->info('No plates expiring soon found.');

            return self::SUCCESS;
        }

        $this->info("Found {$plates->count()} plates expiring soon. Dispatching checks...");

        $jobs = $plates->map(fn ($plate) => new CheckTemporaryPlateExpiryJob($plate));

        Bus::batch($jobs)
            ->allowFailures()
            ->dispatch();

        $this->info("Dispatched {$jobs->count()} jobs.");

        return self::SUCCESS;
    }
}
