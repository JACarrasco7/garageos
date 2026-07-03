<?php

namespace App\Modules\Marketplace\Models;

use Illuminate\Database\Eloquent\Model;

class Valuation extends Model
{
    protected $fillable = [
        'brand',
        'model',
        'year',
        'mileage_km',
        'fuel_type',
        'power_hp',
        'estimated_value',
        'min_value',
        'max_value',
        'confidence_score',
        'data_source',
        'last_updated',
    ];

    protected $casts = [
        'year' => 'integer',
        'mileage_km' => 'integer',
        'power_hp' => 'integer',
        'estimated_value' => 'decimal:2',
        'min_value' => 'decimal:2',
        'max_value' => 'decimal:2',
        'confidence_score' => 'decimal:2',
        'last_updated' => 'datetime',
    ];

    public function scopeByBrand($query, $brand)
    {
        return $query->where('brand', $brand);
    }

    public function scopeByModel($query, $model)
    {
        return $query->where('model', $model);
    }

    public function scopeRecent($query)
    {
        return $query->where('last_updated', '>=', now()->subDays(30));
    }

    public static function calculate(array $vehicleData): self
    {
        $basePrice = match ($vehicleData['brand']) {
            'Volkswagen', 'BMW', 'Mercedes-Benz' => 25000,
            'Audi', 'Porsche' => 35000,
            'Seat', 'Skoda' => 20000,
            default => 15000,
        };

        $ageFactor = max(0, 1 - (($vehicleData['year'] - 2015) * 0.08));
        $mileageFactor = max(0, 1 - ($vehicleData['mileage_km'] / 300000) * 0.3);

        $estimatedValue = $basePrice * $ageFactor * $mileageFactor;
        $confidence = 0.7 + ($ageFactor * 0.2) + ($mileageFactor * 0.1);

        return static::updateOrCreate(
            [
                'brand' => $vehicleData['brand'],
                'model' => $vehicleData['model'],
                'year' => $vehicleData['year'],
            ],
            [
                'mileage_km' => $vehicleData['mileage_km'],
                'fuel_type' => $vehicleData['fuel_type'] ?? 'gasoline',
                'power_hp' => $vehicleData['power_hp'] ?? 100,
                'estimated_value' => round($estimatedValue, 2),
                'min_value' => round($estimatedValue * 0.85, 2),
                'max_value' => round($estimatedValue * 1.15, 2),
                'confidence_score' => round($confidence, 2),
                'data_source' => 'calculated',
                'last_updated' => now(),
            ]
        );
    }
}
