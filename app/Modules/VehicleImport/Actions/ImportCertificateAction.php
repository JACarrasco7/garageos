<?php

namespace App\Modules\VehicleImport\Actions;

use App\Modules\VehicleImport\Models\VehicleImport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportCertificateAction
{
    public function execute(VehicleImport $import): string
    {
        $valuationAction = new ImportValuationAction;
        $valuation = $valuationAction->execute($import);

        $pdf = Pdf::loadView('pdf.import-certificate', [
            'import' => $import,
            'valuation' => $valuation,
            'plate_new' => $import->plate_new ?? app(GenerateSpanishPlateAction::class)->execute(),
        ]);

        $filename = "certificates/import-{$import->id}-".Str::random(8).'.pdf';
        Storage::disk('public')->put($filename, $pdf->output());

        return $filename;
    }
}
