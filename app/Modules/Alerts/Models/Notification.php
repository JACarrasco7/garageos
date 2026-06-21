<?php

namespace App\Modules\Alerts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'vehicle_id',
        'type',
        'title',
        'body',
        'channel',
        'data',
        'read_at',
        'sent_at',
    ];

    protected $casts = [
        'data' => 'json',
        'read_at' => 'timestamp',
        'sent_at' => 'timestamp',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Vehicle\Models\Vehicle::class);
    }

    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }
}
