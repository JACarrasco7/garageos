<?php

namespace App\Modules\Marketplace\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Vehicle\Models\Vehicle;
use App\Modules\Marketplace\Actions\GenerateCertificateAction;
use App\Modules\Marketplace\Models\SaleReport;
use Inertia\Inertia;
use Inertia\Response;

class SaleReportController extends Controller
{
    public function __construct(
        private GenerateCertificateAction $generateCertificate
    ) {}

    public function create(Vehicle $vehicle): Response
    {
        $this->authorize('view', $vehicle->garage);

        return Inertia::render('Marketplace/Generate', [
            'vehicle' => $vehicle->load(['specs', 'documents', 'maintenanceEntries']),
        ]);
    }

    public function store(Vehicle $vehicle): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('view', $vehicle->garage);

        $report = $this->generateCertificate->execute($vehicle);

        return redirect()->route('marketplace.show', $report->token)
            ->with('success', 'Informe generado correctamente');
    }

    public function show(string $token): Response
    {
        $report = SaleReport::where('token', $token)
            ->where('is_active', true)
            ->firstOrFail();

        if ($report->isExpired()) {
            abort(404, 'Informe expirado');
        }

        $report->incrementViews();

        return Inertia::render('Marketplace/Show', [
            'report' => $report->load('vehicle.specs'),
            'vehicle' => $report->vehicle,
        ]);
    }

    public function download(SaleReport $report)
    {
        $this->authorize('view', $report->vehicle->garage);

        $path = storage_path('app/public/' . $report->pdf_path);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->download($path);
    }

    public function showPublic(string $token): Response
    {
        $report = SaleReport::where('token', $token)
            ->where('is_active', true)
            ->firstOrFail();

        if ($report->isExpired()) {
            abort(404, 'Informe expirado');
        }

        $report->incrementViews();

        return Inertia::render('Marketplace/PublicReport', [
            'report' => $report->load('vehicle.specs'),
            'vehicle' => $report->vehicle,
        ]);
    }
}
