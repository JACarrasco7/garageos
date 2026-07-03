<?php

namespace App\Modules\VehicleImport\Console;

use App\Modules\VehicleImport\Jobs\CheckTemporaryPlateExpiryJob;
use App\Modules\VehicleImport\Models\TemporaryPlate;
use Illuminate\Console\Command;

class CheckTemporaryPlateExpiryCommand extends Command
{
    protected $signature = 'imports:check-plates {--days=7 : Days before expiry to check}';

    protected $description = 'Check temporary plates expiring soon';

    public function handle(): int
    {
        $plates = TemporaryPlate::expiringSoon($this->option('days'))->get();

        $this->info("Found {$plates->count()} plates expiring soon");

        foreach ($plates as $plate) {
            CheckTemporaryPlateExpiryJob::dispatch($plate);
            $this->line("Dispatched job for plate: {$plate->plate_number}");
        }

        return self::SUCCESS;
    }
}
