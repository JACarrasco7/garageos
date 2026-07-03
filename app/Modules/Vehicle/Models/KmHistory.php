<?php

namespace App\Modules\Vehicle\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KmHistory extends Model
{
    use HasFactory;

    protected $table = 'km_history';

    protected $fillable = [
        'vehicle_id',
        'km',
        'recorded_at',
        'source',
        'notes',
    ];

    protected $casts = [
        'km' => 'integer',
        'recorded_at' => 'date',
        'source' => 'string',
    ];

    /**
     * Get the vehicle that owns this history entry.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
