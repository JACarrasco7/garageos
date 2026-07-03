<?php

namespace App\Modules\VehicleImport\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemporaryPlate extends Model
{
    protected $table = 'temporary_plates';

    protected $fillable = [
        'import_id',
        'plate_number',
        'issued_at',
        'expires_at',
        'is_extended',
    ];

    protected $casts = [
        'issued_at' => 'date',
        'expires_at' => 'date',
        'is_extended' => 'boolean',
    ];

    protected $attributes = [
        'is_extended' => false,
    ];

    public function vehicleImport(): BelongsTo
    {
        return $this->belongsTo(VehicleImport::class, 'import_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isExpiringSoon(int $days = 7): bool
    {
        return $this->expires_at->subDays($days)->isPast()
            && ! $this->isExpired();
    }

    public function getDaysUntilExpiry(): int
    {
        return Carbon::now()->diffInDays($this->expires_at, false);
    }

    public function canBeExtended(): bool
    {
        return ! $this->is_extended && ! $this->isExpired();
    }

    public function extend(int $days = 30): self
    {
        $this->update([
            'expires_at' => $this->expires_at->addDays($days),
            'is_extended' => true,
        ]);

        return $this;
    }

    public function scopeExpiringSoon($query, int $days = 7)
    {
        return $query->where('expires_at', '<=', now()->addDays($days))
            ->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now());
    }
}
