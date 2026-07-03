<?php

namespace App\Modules\Listings\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Listing extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'created_by_user_id',
        'source_url',
        'source_portal',
        'source_listing_id',
        'brand',
        'model',
        'model_description',
        'year',
        'mileage_km',
        'fuel_type',
        'power_hp',
        'co2_emissions',
        'gearbox',
        'price_eur',
        'country',
        'seller_type',
        'seller_name',
        'seller_location',
        'lat',
        'lng',
        'photos',
        'raw_extracted_data',
        'extraction_method',
        'extraction_status',
        'extraction_error',
        'is_active',
        'last_checked_at',
    ];

    protected $casts = [
        'photos' => 'array',
        'raw_extracted_data' => 'array',
        'year' => 'integer',
        'mileage_km' => 'integer',
        'power_hp' => 'integer',
        'co2_emissions' => 'integer',
        'price_eur' => 'decimal:2',
        'is_active' => 'boolean',
        'last_checked_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<User, Listing>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * Full-text search scope (PostgreSQL only).
     * Falls back to LIKE search on SQLite for tests.
     */
    public function scopeFullText($query, string $search)
    {
        $driver = $query->getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            return $query->whereRaw(
                'search_vector @@ plainto_tsquery(\'spanish\', ?)',
                [$search]
            )->orderByRaw(
                'ts_rank(search_vector, plainto_tsquery(\'spanish\', ?)) DESC',
                [$search]
            );
        }

        return $query->where(function ($q) use ($search) {
            $like = '%'.$search.'%';
            $q->where('title', 'like', $like)
              ->orWhere('description', 'like', $like)
              ->orWhere('brand', 'like', $like)
              ->orWhere('model', 'like', $like);
        });
    }

    /**
     * Scope: Find listings within a radius (in kilometers) from a point.
     * Uses PostGIS ST_DWithin for indexed spatial queries on PostgreSQL.
     * Falls back to bounding-box filter on SQLite for tests.
     */
    public function scopeNearby($query, float $lat, float $lng, int $radiusKm = 25)
    {
        $query->where('is_active', true);

        if ($query->getConnection()->getDriverName() === 'pgsql') {
            return $query->whereRaw(
                'ST_DWithin(location, ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography, ?)',
                [$lng, $lat, $radiusKm * 1000]
            )->orderByRaw(
                'ST_Distance(location, ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography) ASC',
                [$lng, $lat]
            );
        }

        $latDelta = $radiusKm / 111.0;
        $lngDelta = $radiusKm / (111.0 * max(cos(deg2rad($lat)), 0.01));

        return $query->whereBetween('lat', [$lat - $latDelta, $lat + $latDelta])
            ->whereBetween('lng', [$lng - $lngDelta, $lng + $lngDelta]);
    }

    /**
     * Get the distance in km from a point to this listing.
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
