<?php

namespace App\Modules\Maintenance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceEntry extends Model
{
    protected $fillable = [
        'vehicle_id',
        'document_id',
        'workshop_id',
        'type',
        'title',
        'description',
        'km_at_service',
        'service_date',
        'cost',
        'is_verified',
        'notes',
    ];

    protected $casts = [
        'km_at_service' => 'integer',
        'service_date' => 'date',
        'cost' => 'decimal:2',
        'is_verified' => 'boolean',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Vehicle\Models\Vehicle::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Documents\Models\Document::class);
    }

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }
}