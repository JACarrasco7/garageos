<?php

namespace App\Modules\Maintenance\Listeners;

use App\Modules\Maintenance\Events\RevisionCompleted;
use App\Modules\Marketplace\Actions\CalculateScoreAction;

class UpdateVehicleScore
{
    public function __construct(
        private CalculateScoreAction $calculateScore
    ) {}

    public function handle(RevisionCompleted $event): void
    {
        // Recalcular score del vehículo después de mantenimiento
        $this->calculateScore->execute($event->entry->vehicle);
    }
}