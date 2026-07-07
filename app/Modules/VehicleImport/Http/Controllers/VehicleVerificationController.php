<?php

namespace App\Modules\VehicleImport\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\VehicleImport\Models\VehicleImport;
use App\Modules\VehicleImport\Models\VehicleVerification;
use App\Modules\VehicleImport\Services\VerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VehicleVerificationController extends Controller
{
    public function __construct(
        protected VerificationService $verificationService
    ) {}

    public function index(VehicleImport $vehicleImport): Response
    {
        $this->authorize('view', $vehicleImport);

        return Inertia::render('Import/Verification', [
            'import' => $vehicleImport,
            'verification' => $vehicleImport->verification ?? new VehicleVerification,
            'evaluation' => $this->verificationService->evaluateVerification($vehicleImport),
        ]);
    }

    public function update(Request $request, VehicleImport $vehicleImport): JsonResponse
    {
        $this->authorize('update', $vehicleImport);

        $validated = $request->validate([
            'vin_verified' => 'boolean',
            'ownership_verified' => 'boolean',
            'technical_data_verified' => 'boolean',
            'itv_verified' => 'boolean',
            'legal_status_verified' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $verification = VehicleVerification::updateOrCreate(
            ['vehicle_import_id' => $vehicleImport->id],
            array_merge($validated, [
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ])
        );

        // Actualizar el estado general basado en el score
        $evaluation = $this->verificationService->evaluateVerification($vehicleImport);
        $verification->update(['overall_status' => $evaluation['status']]);

        return response()->json([
            'verification' => $verification,
            'evaluation' => $evaluation,
        ]);
    }

    public function generateReport(VehicleImport $vehicleImport): JsonResponse
    {
        $this->authorize('update', $vehicleImport);

        try {
            $pdfPath = $this->verificationService->generateCertificate($vehicleImport);

            return response()->json([
                'success' => true,
                'pdf_url' => Storage::url($pdfPath),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function generateDetailedReport(VehicleImport $vehicleImport): JsonResponse
    {
        $this->authorize('update', $vehicleImport);

        try {
            $pdfPath = $this->verificationService->generateDetailedReport($vehicleImport);

            return response()->json([
                'success' => true,
                'pdf_url' => Storage::url($pdfPath),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
