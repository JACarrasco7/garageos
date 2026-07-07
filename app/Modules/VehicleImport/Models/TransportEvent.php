<?php

namespace App\Modules\VehicleImport\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransportEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_import_id',
        'status',
        'location',
        'latitude',
        'longitude',
        'note',
        'photo_path',
        'occurred_at',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function vehicleImport(): BelongsTo
    {
        return $this->belongsTo(VehicleImport::class);
    }
}
