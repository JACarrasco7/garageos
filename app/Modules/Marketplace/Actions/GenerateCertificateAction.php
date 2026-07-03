<?php

namespace App\Modules\Marketplace\Actions;

use App\Modules\Marketplace\Models\SaleReport;
use App\Modules\Vehicle\Models\Vehicle;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class GenerateCertificateAction
{
    public function execute(Vehicle $vehicle): SaleReport
    {
        $score = app(CalculateScoreAction::class)->execute($vehicle);

        // Generar PDF
        $pdf = Pdf::loadView('pdf.sale-report', [
            'vehicle' => $vehicle->load(['specs', 'documents', 'maintenanceEntries']),
            'score' => $score,
        ]);

        $filename = 'reports/sale-report-'.$vehicle->id.'-'.time().'.pdf';
        $pdf->save(storage_path('app/public/'.$filename));

        return SaleReport::create([
            'vehicle_id' => $vehicle->id,
            'token' => Str::random(64),
            'score' => $score,
            'pdf_path' => $filename,
            'expires_at' => now()->addDays(30),
        ]);
    }
}
