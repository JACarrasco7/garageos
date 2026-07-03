<?php

namespace App\Modules\Maintenance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceInterval extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand',
        'model',
        'type',
        'interval_km',
        'interval_months',
        'description',
    ];

    protected $casts = [
        'interval_km' => 'integer',
        'interval_months' => 'integer',
    ];

    /**
     * Scope a query to only include generic intervals.
     */
    public function scopeGeneric($query)
    {
        return $query->whereNull('brand')->whereNull('model');
    }

    /**
     * Scope a query to include intervals for a specific vehicle.
     */
    public function scopeForVehicle($query, $brand, $model)
    {
        return $query->where(function ($q) use ($brand, $model) {
            $q->where('brand', $brand)
                ->where('model', $model);
        });
    }
}
