<?php

namespace App\Modules\VehicleImport\Services;

use App\Modules\VehicleImport\Models\VehicleImport;
use App\Modules\VehicleImport\Models\VehicleVerification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class VerificationService
{
    /**
     * Evalúa el estado de verificación del vehículo y genera el score de confianza.
     */
    public function evaluateVerification(VehicleImport $import): array
    {
        $verification = $import->verification ?: new VehicleVerification([
            'vehicle_import_id' => $import->id,
            'verified_by' => auth()->id(),
        ]);

        $score = 0;
        $totalPoints = 5;

        if ($verification->vin_verified) {
            $score++;
        }
        if ($verification->ownership_verified) {
            $score++;
        }
        if ($verification->technical_data_verified) {
            $score++;
        }
        if ($verification->itv_verified) {
            $score++;
        }
        if ($verification->legal_status_verified) {
            $score++;
        }

        $percentage = ($score / $totalPoints) * 100;

        return [
            'score' => $percentage,
            'status' => $this->determineStatus($percentage),
            'points' => [
                'vin' => $verification->vin_verified,
                'ownership' => $verification->ownership_verified,
                'technical' => $verification->technical_data_verified,
                'itv' => $verification->itv_verified,
                'legal' => $verification->legal_status_verified,
            ],
        ];
    }

    protected function determineStatus(float $percentage): string
    {
        if ($percentage === 100.0) {
            return 'CERTIFIED';
        }
        if ($percentage >= 70.0) {
            return 'VERIFIED';
        }
        if ($percentage >= 40.0) {
            return 'PARTIAL';
        }

        return 'UNVERIFIED';
    }

    /**
     * Genera el PDF del Certificado de Verificación GarageOS (Resumen Ejecutivo).
     */
    public function generateCertificate(VehicleImport $import): string
    {
        $verification = $import->verification;
        if (! $verification) {
            throw new \Exception('No existe una verificación previa para generar el certificado.');
        }

        $data = [
            'import' => $import,
            'verification' => $verification,
            'auditor' => $verification->auditor,
            'certificate_id' => 'GOS-'.strtoupper(uniqid()),
            'date' => now()->format('d/m/Y'),
            'score' => $this->evaluateVerification($import)['score'],
        ];

        $pdf = Pdf::loadView('documents.verification-report', $data);

        $fileName = 'reports/cert_'.$import->id.'_'.time().'.pdf';
        Storage::put($fileName, $pdf->output());

        $verification->update(['report_pdf_path' => $fileName]);

        // Auto-create H2 milestone when CERTIFIED
        if ($verification->overall_status === 'CERTIFIED') {
            $contract = $import->contract;
            $amount = $contract?->agreed_price * 0.50 ?? 0;
            $import->paymentMilestones()->firstOrCreate(
                ['milestone' => 'H2_compra'],
                ['amount' => $amount, 'status' => 'pending']
            );
        }

        return $fileName;
    }

    /**
     * Genera el PDF del Informe Técnico Detallado (Auditoría Completa).
     */
    public function generateDetailedReport(VehicleImport $import): string
    {
        $verification = $import->verification;
        if (! $verification) {
            throw new \Exception('No existe una verificación previa para generar el informe detallado.');
        }

        $data = [
            'import' => $import,
            'verification' => $verification,
            'auditor' => $verification->auditor,
            'certificate_id' => 'AUD-'.strtoupper(uniqid()),
            'date' => now()->format('d/m/Y'),
        ];

        $pdf = Pdf::loadView('documents.detailed-report', $data);

        $fileName = 'reports/audit_'.$import->id.'_'.time().'.pdf';
        Storage::put($fileName, $pdf->output());

        return $fileName;
    }
}
