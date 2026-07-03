<?php

namespace App\Modules\Marketplace\Models;

use App\Models\User;
use App\Modules\Vehicle\Models\Vehicle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class MarketplaceListing extends Model
{
    use Searchable;

    protected $table = 'marketplace_listings';

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'title',
        'description',
        'price',
        'currency',
        'is_negotiable',
        'location_city',
        'location_region',
        'location_lat',
        'location_lng',
        'status',
        'views',
        'featured_until',
        'expires_at',
        'photos',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_negotiable' => 'boolean',
        'location_lat' => 'decimal:7',
        'location_lng' => 'decimal:7',
        'featured_until' => 'datetime',
        'expires_at' => 'datetime',
        'photos' => 'array',
        'status' => 'string',
    ];

    protected $dates = [
        'featured_until',
        'expires_at',
        'created_at',
        'updated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(MarketplaceFavorite::class, 'listing_id');
    }

    public function isFavoritedBy(User $user): bool
    {
        return $this->favorites()->where('user_id', $user->id)->exists();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('expires_at', '>', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured_until', '>', now());
    }

    public function scopeNearby($query, $lat, $lng, $radius = 50)
    {
        return $query->whereRaw('
            (6371 * acos(cos(radians(?)) * cos(radians(location_lat))
            * cos(radians(location_lng) - radians(?))
            + sin(radians(?)) * sin(radians(location_lat)))) < ?
        ', [$lat, $lng, $lat, $radius]);
    }

    public function toSearchableArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'brand' => $this->vehicle->brand ?? '',
            'model' => $this->vehicle->model ?? '',
            'year' => $this->vehicle->year ?? null,
            'price' => (float) $this->price,
            'location_city' => $this->location_city,
            'location_region' => $this->location_region,
            'status' => $this->status,
        ];
    }
}
