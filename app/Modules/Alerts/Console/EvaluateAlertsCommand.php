<?php

namespace App\Modules\Alerts\Console;

use App\Modules\Alerts\Jobs\EvaluateAlertsJob;
use Illuminate\Console\Command;

class EvaluateAlertsCommand extends Command
{
    protected $signature = 'alerts:evaluate';
    protected $description = 'Evaluate vehicle alert rules and trigger notifications';

    public function handle(): int
    {
        EvaluateAlertsJob::dispatch();
        $this->info('Alert evaluation dispatched to queue');
        return self::SUCCESS;
    }
}
