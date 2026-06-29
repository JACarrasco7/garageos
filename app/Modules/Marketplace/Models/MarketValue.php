<?php

namespace App\Modules\Marketplace\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketValue extends Model
{
    protected $fillable = [
        'vehicle_id',
        'estimated_value',
        'min_value',
        'max_value',
        'source',
        'recorded_at',
    ];

    protected $casts = [
        'estimated_value' => 'decimal:2',
        'min_value' => 'decimal:2',
        'max_value' => 'decimal:2',
        'recorded_at' => 'timestamp',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Vehicle\Models\Vehicle::class);
    }
}