<?php

namespace App\Modules\VehicleImport\Http\Controllers;

use App\Modules\VehicleImport\Enums\ImportStep;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleImportResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'vehicle_id' => $this->vehicle_id,
            'listing_id' => $this->listing_id,
            'plate_original' => $this->plate_original,
            'plate_new' => $this->plate_new,
            'brand' => $this->brand,
            'model' => $this->model,
            'year' => $this->year,
            'engine_cc' => $this->engine_cc,
            'power_kw' => $this->power_kw,
            'co2_emissions' => $this->co2_emissions,
            'origin_country' => $this->origin_country,
            'purchase_date' => $this->purchase_date?->toDateString(),
            'arrival_date' => $this->arrival_date?->toDateString(),
            'itv_deadline' => $this->itv_deadline?->toDateString(),
            'current_step' => $this->current_step->value,
            'current_step_label' => $this->current_step->getLabel(),
            'current_step_order' => $this->current_step->getOrder(),
            'needs_homologation' => $this->needs_homologation,
            'status' => $this->status,
            'documents' => $this->documents,
            'rejection_reason' => $this->rejection_reason,
            'progress_percentage' => $this->getProgressPercentage(),
            'documents_count' => $this->importDocuments()->count(),
            'verified_documents_count' => $this->importDocuments()->where('is_verified', true)->count(),
            'temporary_plates' => $this->temporaryPlates->map(fn ($plate) => [
                'id' => $plate->id,
                'plate_number' => $plate->plate_number,
                'issued_at' => $plate->issued_at->toDateString(),
                'expires_at' => $plate->expires_at->toDateString(),
                'is_expired' => $plate->isExpired(),
                'is_expiring_soon' => $plate->isExpiringSoon(),
                'days_until_expiry' => $plate->getDaysUntilExpiry(),
                'can_be_extended' => $plate->canBeExtended(),
            ]),
            'can_advance_to' => collect($this->current_step->getNext())
                ->filter(fn ($step) => $this->canAdvanceToStep($step))
                ->map(fn ($step) => [
                    'value' => $step->value,
                    'label' => $step->getLabel(),
                ])
                ->first(),
            'step_completion' => collect([
                'purchase',
                'transport',
                'itv_inspection',
                'taxes',
                'dgt_registration',
                'plates',
            ])
                ->mapWithKeys(fn ($step) => [
                    $step => $this->isStepCompleted(ImportStep::from($step)),
                ]),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
