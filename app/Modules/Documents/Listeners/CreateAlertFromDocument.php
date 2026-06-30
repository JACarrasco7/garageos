<?php

namespace App\Modules\Documents\Listeners;

use App\Modules\Documents\Events\DocumentProcessed;
use App\Modules\Alerts\Models\AlertRule;

class CreateAlertFromDocument
{
    public function handle(DocumentProcessed $event): void
    {
        $document = $event->document;

        // Crear alerta automática para ITV
        if ($document->type === 'itv' && $document->expiry_date) {
            AlertRule::firstOrCreate([
                'vehicle_id' => $document->vehicle_id,
                'type' => 'itv',
            ], [
                'trigger_date' => $document->expiry_date,
                'advance_days' => 30,
            ]);
        }

        // Crear alerta automática para Seguro
        if ($document->type === 'seguro' && $document->expiry_date) {
            AlertRule::firstOrCreate([
                'vehicle_id' => $document->vehicle_id,
                'type' => 'seguro',
            ], [
                'trigger_date' => $document->expiry_date,
                'advance_days' => 30,
            ]);
        }
    }
}