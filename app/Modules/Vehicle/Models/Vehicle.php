<?php

namespace App\Modules\Vehicle\Models;

use App\Enums\FuelType;
use App\Modules\Alerts\Models\AlertRule;
use App\Modules\Documents\Models\Document;
use App\Modules\Identity\Models\Garage;
use App\Modules\Maintenance\Models\MaintenanceEntry;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Vehicle extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use Searchable;

    protected $fillable = [
        'garage_id',
        'plate',
        'vin',
        'qr_token',
        'brand',
        'model',
        'year',
        'registration_date',
        'fuel_type',
        'eco_label',
        'emissions_co2',
        'official_consumption',
        'color',
        'current_km',
        'purchase_date',
        'purchase_price',
        'photo',
        'is_active',
    ];

    protected $casts = [
        'fuel_type' => FuelType::class,
        'eco_label' => 'string',
        'year' => 'integer',
        'emissions_co2' => 'integer',
        'current_km' => 'integer',
        'registration_date' => 'date',
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
        'official_consumption' => 'decimal:1',
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
        return $this->belongsTo(Garage::class);
    }

    public function photos()
    {
        return $this->hasMany(VehiclePhoto::class)->orderBy('sort_order');
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
        return $this->hasMany(Document::class);
    }

    public function maintenanceEntries(): HasMany
    {
        return $this->hasMany(MaintenanceEntry::class);
    }

    public function alertRules(): HasMany
    {
        return $this->hasMany(AlertRule::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->useDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(200)
            ->height(200)
            ->sharpen(10)
            ->performOnCollections('photo');

        $this->addMediaConversion('medium')
            ->width(600)
            ->height(400)
            ->sharpen(10)
            ->performOnCollections('photo');

        $this->addMediaConversion('large')
            ->width(1200)
            ->height(800)
            ->sharpen(10)
            ->performOnCollections('photo');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('photo');
    }

    public function getPhotoThumbnailUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('photo', 'thumb');
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

    public function scopeByBrand($query, $brand)
    {
        return $query->where('brand', $brand);
    }

    public function scopeByFuelType($query, $fuelType)
    {
        return $query->where('fuel_type', $fuelType);
    }

    public function scopeByYearRange($query, $from, $to)
    {
        return $query->whereBetween('year', [$from, $to]);
    }

    public function scopeByGarage($query, $garageId)
    {
        return $query->where('garage_id', $garageId);
    }

    public function lastOilChangeKm(): ?int
    {
        return $this->maintenanceEntries()
            ->where('type', 'aceite')
            ->latest('service_date')
            ->value('km_at_service');
    }

    public function lastFilterChangeKm(): ?int
    {
        return $this->maintenanceEntries()
            ->where('type', 'filtros')
            ->latest('service_date')
            ->value('km_at_service');
    }

    public function lastTireChangeKm(): ?int
    {
        return $this->maintenanceEntries()
            ->where('type', 'neumaticos')
            ->latest('service_date')
            ->value('km_at_service');
    }

    public function lastBrakeChangeKm(): ?int
    {
        return $this->maintenanceEntries()
            ->where('type', 'frenos')
            ->latest('service_date')
            ->value('km_at_service');
    }
}
