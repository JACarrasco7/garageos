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
}
