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
            'fuel_type' => $this->fuel_type,
            'color' => $this->color,
            'current_km' => $this->current_km,
            'is_active' => $this->is_active,
            'specs' => new VehicleSpecResource($this->whenLoaded('specs')),
            'documents_count' => $this->documents_count ?? $this->documents()->count(),
            'maintenance_entries_count' => $this->maintenance_entries()->count(),
        ];
    }
}