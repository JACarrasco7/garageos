<?php

namespace App\Modules\Maintenance\Models;

use App\Modules\Documents\Models\Document;
use App\Modules\Vehicle\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceEntry extends Model
{
    use HasFactory;

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

    /**
     * Get the vehicle that owns this maintenance entry.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the document associated with this maintenance entry.
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Get the workshop where this maintenance was performed.
     */
    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }
}
