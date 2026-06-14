<?php

namespace App\Modules\Maintenance\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceInterval extends Model
{
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

    public function scopeGeneric($query)
    {
        return $query->whereNull('brand')->whereNull('model');
    }

    public function scopeForVehicle($query, $brand, $model)
    {
        return $query->where(function ($q) use ($brand, $model) {
            $q->where('brand', $brand)->where('model', $model)
              ->orWhereNull('brand')->whereNull('model');
        });
    }
}