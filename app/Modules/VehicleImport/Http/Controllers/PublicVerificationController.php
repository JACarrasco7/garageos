<?php

namespace App\Modules\VehicleImport\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\VehicleImport\Models\VehicleVerification;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class PublicVerificationController extends Controller
{
    /**
     * Show public verification certificate.
     */
    public function show(string $certificateId): Response|JsonResponse
    {
        $verification = VehicleVerification::where('report_pdf_path', 'like', "%{$certificateId}%")
            ->orWhereHas('vehicleImport', function ($q) use ($certificateId) {
                $q->where('id', $certificateId);
            })
            ->with(['vehicleImport', 'auditor'])
            ->firstOrFail();

        return Inertia::render('Import/PublicVerification', [
            'verification' => $verification,
            'import' => $verification->vehicleImport,
            'auditor' => $verification->auditor,
        ]);
    }
}
