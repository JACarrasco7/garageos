<?php

namespace App\Modules\Documents\Listeners;

use App\Modules\Documents\Events\DocumentProcessed;
use App\Modules\Maintenance\Models\MaintenanceEntry;

class CreateMaintenanceFromDocument
{
    public function handle(DocumentProcessed $event): void
    {
        $document = $event->document;

        // Si es una factura, crear entrada de mantenimiento
        if ($document->type === 'factura' && $document->parsed_data) {
            $data = $document->parsed_data;

            // Detectar tipo de mantenimiento del texto OCR
            $type = $this->detectMaintenanceType($data['text'] ?? '');

            if ($type) {
                MaintenanceEntry::create([
                    'vehicle_id' => $document->vehicle_id,
                    'document_id' => $document->id,
                    'type' => $type,
                    'title' => $document->title,
                    'description' => $data['text'] ?? null,
                    'km_at_service' => $document->km_at_time,
                    'service_date' => $document->document_date,
                    'cost' => $document->amount,
                ]);
            }
        }
    }

    private function detectMaintenanceType(string $text): ?string
    {
        $text = strtolower($text);

        if (str_contains($text, 'aceite') || str_contains($text, 'oil')) {
            return 'aceite';
        }

        if (str_contains($text, 'filtro') || str_contains($text, 'filter')) {
            return 'filtros';
        }

        if (str_contains($text, 'neumatico') || str_contains($text, 'tire')) {
            return 'neumaticos';
        }

        return null;
    }
}
