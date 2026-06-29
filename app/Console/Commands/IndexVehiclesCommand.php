<?php

namespace App\Console\Commands;

use App\Modules\Vehicle\Models\Vehicle;
use Illuminate\Console\Command;

class IndexVehiclesCommand extends Command
{
    protected $signature = 'vehicles:index';
    protected $description = 'Index all vehicles in Meilisearch';

    public function handle(): int
    {
        Vehicle::chunk(100, fn($vehicles) => $vehicles->searchable());

        $this->info('Vehicles indexed successfully');

        return 0;
    }
}
