<?php

namespace App\Modules\Alerts\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlertRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'type',
        'trigger_km',
        'trigger_date',
        'advance_days',
        'advance_km',
        'is_active',
        'last_triggered',
    ];

    protected $casts = [
        'trigger_km' => 'integer',
        'trigger_date' => 'date',
        'advance_days' => 'integer',
        'advance_km' => 'integer',
        'is_active' => 'boolean',
        'last_triggered' => 'timestamp',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Vehicle\Models\Vehicle::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
