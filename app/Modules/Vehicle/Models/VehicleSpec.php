<?php

namespace App\Modules\Vehicle\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleSpec extends Model
{
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

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
