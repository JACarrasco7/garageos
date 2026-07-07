<?php

namespace App\Modules\Marketplace\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketplaceFavorite extends Model
{
    protected $table = 'marketplace_favorites';

    protected $fillable = [
        'user_id',
        'listing_id',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'listing_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(MarketplaceListing::class, 'listing_id');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByListing($query, $listingId)
    {
        return $query->where('listing_id', $listingId);
    }
}
