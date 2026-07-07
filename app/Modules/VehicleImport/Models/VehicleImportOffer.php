<?php

namespace App\Modules\VehicleImport\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleImportOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_import_request_id',
        'user_id',
        'price',
        'delivery_time_days',
        'warranty_months',
        'description',
        'status',
        'is_boosted',
        'boosted_until',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'delivery_time_days' => 'integer',
        'warranty_months' => 'integer',
        'is_boosted' => 'boolean',
        'boosted_until' => 'timestamp',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(VehicleImportRequest::class, 'vehicle_import_request_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(VehicleImportRating::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    protected $appends = ['rating_avg', 'rating_count'];

    public function getRatingAvgAttribute(): float
    {
        return $this->ratings()->avg('rating') ?? 0;
    }

    public function getRatingCountAttribute(): int
    {
        return $this->ratings()->count();
    }
}
