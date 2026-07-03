<?php

namespace App\Modules\Marketplace\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SearchAlert extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'brand',
        'min_price',
        'max_price',
        'min_year',
        'max_year',
        'fuel_type',
        'location',
        'active',
        'last_checked_at',
    ];

    protected $casts = [
        'min_price' => 'decimal:2',
        'max_price' => 'decimal:2',
        'active' => 'boolean',
        'last_checked_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function matchesListing(MarketplaceListing $listing): bool
    {
        if ($this->brand && $listing->vehicle?->brand !== $this->brand) {
            return false;
        }

        if ($this->min_price && $listing->price < $this->min_price) {
            return false;
        }

        if ($this->max_price && $listing->price > $this->max_price) {
            return false;
        }

        if ($this->min_year && $listing->vehicle?->year < $this->min_year) {
            return false;
        }

        if ($this->max_year && $listing->vehicle?->year > $this->max_year) {
            return false;
        }

        if ($this->fuel_type && $listing->vehicle?->fuel_type !== $this->fuel_type) {
            return false;
        }

        return true;
    }
}
