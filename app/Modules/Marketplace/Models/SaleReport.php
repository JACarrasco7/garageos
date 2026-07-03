<?php

namespace App\Modules\Marketplace\Models;

use App\Modules\Vehicle\Models\Vehicle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleReport extends Model
{
    protected $fillable = [
        'vehicle_id',
        'token',
        'score',
        'pdf_path',
        'is_active',
        'expires_at',
        'views',
    ];

    protected $casts = [
        'score' => 'integer',
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
        'views' => 'integer',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }
}
