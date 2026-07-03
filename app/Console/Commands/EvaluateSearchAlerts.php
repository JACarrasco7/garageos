<?php

namespace App\Console\Commands;

use App\Modules\Marketplace\Models\MarketplaceListing;
use App\Modules\Marketplace\Models\SearchAlert;
use App\Notifications\SearchAlertNotification;
use Illuminate\Console\Command;

class EvaluateSearchAlerts extends Command
{
    protected $signature = 'alerts:evaluate {--hours=6}';

    protected $description = 'Evaluate search alerts and notify users of new listings';

    public function handle(): int
    {
        $hoursAgo = $this->option('hours');
        $since = now()->subHours($hoursAgo);

        $alerts = SearchAlert::with('user')->where('active', true)->get();

        foreach ($alerts as $alert) {
            $query = MarketplaceListing::query()
                ->active()
                ->where('created_at', '>=', $since);

            if ($alert->brand) {
                $query->whereHas('vehicle', fn ($q) => $q->where('brand', $alert->brand));
            }

            if ($alert->min_price) {
                $query->where('price', '>=', $alert->min_price);
            }

            if ($alert->max_price) {
                $query->where('price', '<=', $alert->max_price);
            }

            if ($alert->min_year) {
                $query->whereHas('vehicle', fn ($q) => $q->where('year', '>=', $alert->min_year));
            }

            if ($alert->max_year) {
                $query->whereHas('vehicle', fn ($q) => $q->where('year', '<=', $alert->max_year));
            }

            $newListings = $query->get();

            if ($newListings->count() > 0) {
                $alert->user->notify(new SearchAlertNotification(
                    $alert->name,
                    $newListings->count(),
                    $newListings->pluck('id')->toArray()
                ));
            }
        }

        $this->info("Evaluated {$alerts->count()} search alerts");

        return Command::SUCCESS;
    }
}
