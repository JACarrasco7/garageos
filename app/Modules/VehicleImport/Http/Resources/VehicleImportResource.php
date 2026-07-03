<?php

namespace App\Modules\VehicleImport\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VehicleImportResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'plate_original' => $this->plate_original,
            'plate_new' => $this->plate_new,
            'brand' => $this->brand,
            'model' => $this->model,
            'year' => $this->year,
            'engine_cc' => $this->engine_cc,
            'power_kw' => $this->power_kw,
            'co2_emissions' => $this->co2_emissions,
            'origin_country' => $this->origin_country,
            'purchase_date' => $this->purchase_date?->format('Y-m-d'),
            'arrival_date' => $this->arrival_date?->format('Y-m-d'),
            'itv_deadline' => $this->itv_deadline?->format('Y-m-d'),
            'current_step' => $this->current_step->value,
            'current_step_label' => $this->current_step->getLabel(),
            'needs_homologation' => $this->needs_homologation,
            'status' => $this->status,
            'documents' => $this->documents,
            'rejection_reason' => $this->rejection_reason,
            'vehicle_id' => $this->vehicle_id,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'can_advance_to_step' => $this->canAdvanceToStep($this->current_step->getNext()),
        ];
    }
}
