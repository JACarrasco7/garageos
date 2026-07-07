<?php

namespace App\Modules\VehicleImport\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleInspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_import_request_id',
        'inspector_id',
        'provider_id',
        'location',
        'scheduled_at',
        'completed_at',
        'report',
        'price',
        'status',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
        'price' => 'decimal:2',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(VehicleImportRequest::class, 'vehicle_import_request_id');
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
