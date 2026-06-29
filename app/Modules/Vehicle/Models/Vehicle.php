<?php

namespace App\Modules\Vehicle\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Vehicle extends Model
{
    use HasFactory;
    use \Laravel\Scout\Searchable;

    protected $fillable = [
        'garage_id',
        'plate',
        'vin',
        'brand',
        'model',
        'year',
        'fuel_type',
        'color',
        'current_km',
        'purchase_date',
        'purchase_price',
        'photo',
        'is_active',
    ];

    protected $casts = [
        'year' => 'integer',
        'current_km' => 'integer',
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($vehicle) {
            if (empty($vehicle->qr_token)) {
                $vehicle->qr_token = Str::random(64);
            }
        });
    }

    public function garage(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Identity\Models\Garage::class);
    }

    public function specs(): HasOne
    {
        return $this->hasOne(VehicleSpec::class);
    }

    public function kmHistory(): HasMany
    {
        return $this->hasMany(KmHistory::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(\App\Modules\Documents\Models\Document::class);
    }

    public function maintenanceEntries(): HasMany
    {
        return $this->hasMany(\App\Modules\Maintenance\Models\MaintenanceEntry::class);
    }

    public function alertRules(): HasMany
    {
        return $this->hasMany(\App\Modules\Alerts\Models\AlertRule::class);
    }

    public function toSearchableArray(): array
    {
        return [
            'brand' => $this->brand,
            'model' => $this->model,
            'plate' => $this->plate,
            'year' => $this->year,
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
