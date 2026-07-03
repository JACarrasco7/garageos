<?php

namespace App\Modules\Maintenance\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workshop extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'address',
        'city',
        'lat',
        'lng',
        'phone',
        'email',
        'is_verified',
        'rating',
        'description',
        'logo',
        'services',
    ];

    protected $casts = [
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
        'is_verified' => 'boolean',
        'services' => 'array',
    ];

    /**
     * Get the user that owns the workshop.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the maintenance entries associated with this workshop.
     */
    public function maintenanceEntries(): HasMany
    {
        return $this->hasMany(MaintenanceEntry::class);
    }

    /**
     * Scope: Find workshops within a radius (in kilometers) from a point.
     * Uses PostGIS ST_DWithin for indexed spatial queries on PostgreSQL.
     * Falls back to bounding-box filter on SQLite (tests).
     */
    public function scopeNearby($query, float $lat, float $lng, int $radiusKm = 25)
    {
        if ($query->getConnection()->getDriverName() === 'pgsql') {
            return $query->whereRaw(
                'ST_DWithin(location, ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography, ?)',
                [$lng, $lat, $radiusKm * 1000]
            )->orderByRaw(
                'ST_Distance(location, ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography) ASC',
                [$lng, $lat]
            );
        }

        // SQLite fallback: bounding box in degrees (fast index scan)
        $latDelta = $radiusKm / 111.0;
        $lngDelta = $radiusKm / (111.0 * max(cos(deg2rad($lat)), 0.01));

        return $query->whereBetween('lat', [$lat - $latDelta, $lat + $latDelta])
            ->whereBetween('lng', [$lng - $lngDelta, $lng + $lngDelta]);
    }

    /**
     * Get the distance in km from a point to this workshop.
     */
    public function distanceFrom(float $lat, float $lng): ?float
    {
        if (! $this->lat || ! $this->lng) {
            return null;
        }

        $earthRadius = 6371;

        $latFrom = deg2rad((float) $this->lat);
        $lonFrom = deg2rad((float) $this->lng);
        $latTo = deg2rad($lat);
        $lonTo = deg2rad($lng);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return round($earthRadius * $angle, 2);
    }
}
