<?php

namespace App\Modules\Vehicle\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'plate' => $this->plate,
            'brand' => $this->brand,
            'model' => $this->model,
            'year' => $this->year,
            'registration_date' => $this->registration_date,
            'fuel_type' => $this->fuel_type,
            'eco_label' => $this->eco_label,
            'emissions_co2' => $this->emissions_co2,
            'official_consumption' => $this->official_consumption,
            'color' => $this->color,
            'current_km' => $this->current_km,
            'is_active' => $this->is_active,
            'specs' => new VehicleSpecResource($this->whenLoaded('specs')),
            'photos' => VehiclePhotoResource::collection($this->whenLoaded('photos')),
            'documents_count' => $this->documents_count ?? $this->documents()->count(),
            'maintenance_entries_count' => $this->maintenance_entries()->count(),
        ];
    }
}
