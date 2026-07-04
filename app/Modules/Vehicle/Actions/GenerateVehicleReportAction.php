<?php

namespace App\Modules\Vehicle\Actions;

use App\Modules\Vehicle\Models\Vehicle;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateVehicleReportAction
{
    public function execute(Vehicle $vehicle): string
    {
        $vehicle->load([
            'specs',
            'documents',
            'maintenanceEntries' => fn ($q) => $q->latest()->limit(50),
            'kmHistory' => fn ($q) => $q->latest()->limit(20),
        ]);

        $stats = [
            'total_maintenance_cost' => $vehicle->maintenanceEntries()->sum('cost'),
            'avg_monthly_km' => $this->calculateAvgMonthlyKm($vehicle),
            'maintenance_count' => $vehicle->maintenanceEntries()->count(),
            'document_count' => $vehicle->documents()->count(),
        ];

        $pdf = Pdf::loadView('pdf.vehicle-report', [
            'vehicle' => $vehicle,
            'stats' => $stats,
        ]);

        $filename = "reports/vehicle-{$vehicle->id}-".Str::random(8).'.pdf';
        Storage::disk('public')->put($filename, $pdf->output());

        return $filename;
    }

    private function calculateAvgMonthlyKm(Vehicle $vehicle): float
    {
        $firstKm = $vehicle->kmHistory()->oldest()->first();
        $latestKm = $vehicle->kmHistory()->latest()->first();

        if (! $firstKm || ! $latestKm) {
            return 0;
        }

        $months = $firstKm->created_at->diffInMonths($latestKm->created_at);

        return $months > 0 ? round(($latestKm->km - $firstKm->km) / $months, 0) : 0;
    }
}
