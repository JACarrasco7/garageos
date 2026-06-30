<?php

namespace App\Modules\Documents\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $fillable = [
        'vehicle_id',
        'type',
        'title',
        'file_path',
        'file_size',
        'mime_type',
        'km_at_time',
        'document_date',
        'expiry_date',
        'amount',
        'parsed_data',
        'extracted_text',
        'is_verified',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'document_date' => 'date',
        'expiry_date' => 'date',
        'amount' => 'decimal:2',
        'parsed_data' => 'json',
        'is_verified' => 'boolean',
        'verified_at' => 'timestamp',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Vehicle\Models\Vehicle::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Identity\Models\User::class, 'verified_by');
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function daysUntilExpiry(): int
    {
        return $this->expiry_date ? now()->diffInDays($this->expiry_date, false) : 0;
    }
}
