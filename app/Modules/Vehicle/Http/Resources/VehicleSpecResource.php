<?php

namespace App\Modules\Vehicle\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VehicleSpecResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'engine_cc' => $this->engine_cc,
            'power_hp' => $this->power_hp,
            'torque_nm' => $this->torque_nm,
            'transmission' => $this->transmission,
            'drive' => $this->drive,
            'doors' => $this->doors,
            'seats' => $this->seats,
        ];
    }
}