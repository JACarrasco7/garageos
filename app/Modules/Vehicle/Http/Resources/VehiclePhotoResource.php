<?php

namespace App\Modules\Vehicle\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VehiclePhotoResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'thumbnail_url' => $this->thumbnail_url,
            'category' => $this->category,
            'caption' => $this->caption,
            'sort_order' => $this->sort_order,
            'file_type' => $this->file_type,
            'file_size' => $this->file_size,
        ];
    }
}
