<?php

namespace App\Modules\Vehicle\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleSpec extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'engine_cc',
        'power_hp',
        'torque_nm',
        'transmission',
        'drive',
        'doors',
        'seats',
    ];

    protected $casts = [
        'engine_cc' => 'integer',
        'power_hp' => 'integer',
        'torque_nm' => 'integer',
        'doors' => 'integer',
        'seats' => 'integer',
    ];

    /**
     * Get the vehicle that owns these specifications.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function scopeByEngineSize($query, $cc)
    {
        return $query->where('engine_cc', $cc);
    }

    public function scopeByPowerRange($query, $minHp, $maxHp)
    {
        return $query->whereBetween('power_hp', [$minHp, $maxHp]);
    }

    public function getPowerKwAttribute(): ?float
    {
        return $this->power_hp ? round($this->power_hp * 0.7457, 2) : null;
    }
}
