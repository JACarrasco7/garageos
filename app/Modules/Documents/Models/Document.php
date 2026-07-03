<?php

namespace App\Modules\Documents\Models;

use App\Models\User;
use App\Modules\Vehicle\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Document extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

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
        'km_at_time' => 'integer',
        'document_date' => 'date',
        'expiry_date' => 'date',
        'amount' => 'decimal:2',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'parsed_data' => 'array',
    ];

    /**
     * Get the vehicle that owns this document.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the user that verified this document.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Check if the document is expired.
     */
    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function daysUntilExpiry(): int
    {
        return $this->expiry_date ? now()->diffInDays($this->expiry_date, false) : 0;
    }
}
