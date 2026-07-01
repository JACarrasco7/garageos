<?php

namespace App\Console\Commands;

use App\Modules\Vehicle\Models\Vehicle;
use App\Modules\Marketplace\Actions\ScrapeMarketValueAction;
use Illuminate\Console\Command;

class ScrapeMarketValuesCommand extends Command
{
    protected $signature = 'market:scrape {--vehicle : ID del vehículo a procesar} {--all : Procesar todos los vehículos}';
    protected $description = 'Scrape market values for vehicles';

    public function handle(ScrapeMarketValueAction $scrape): int
    {
        if ($this->option('vehicle')) {
            $vehicle = Vehicle::find($this->option('vehicle'));
            if (!$vehicle) {
                $this->error('Vehículo no encontrado');
                return 1;
            }
            $scrape->execute($vehicle);
            $this->info("Valor scrapeado para {$vehicle->brand} {$vehicle->model}");
            return 0;
        }

        if ($this->option('all')) {
            $vehicles = Vehicle::all();
            $bar = $this->output->createProgressBar($vehicles->count());
            foreach ($vehicles as $vehicle) {
                $scrape->execute($vehicle);
                $bar->advance();
            }
            $bar->finish();
            $this->newLine();
            $this->info('Valores scrapeados para todos los vehículos');
            return 0;
        }

        $this->error('Debes especificar --vehicle o --all');
        return 1;
    }
}