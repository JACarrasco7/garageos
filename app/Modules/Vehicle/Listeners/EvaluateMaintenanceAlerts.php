<?php

namespace App\Modules\Vehicle\Listeners;

use App\Modules\Alerts\Jobs\EvaluateAlertsJob;
use App\Modules\Vehicle\Events\KmUpdated;

class EvaluateMaintenanceAlerts
{
    public function handle(KmUpdated $event): void
    {
        // Disparar evaluación de alertas cuando cambia el km
        EvaluateAlertsJob::dispatch();
    }
}
