<?php

namespace App\Modules\Maintenance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workshop extends Model
{
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
        'rating' => 'integer',
        'services' => 'json',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function maintenanceEntries(): HasMany
    {
        return $this->hasMany(MaintenanceEntry::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(WorkshopReview::class);
    }
}
