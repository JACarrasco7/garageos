<?php

namespace App\Modules\VehicleImport\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleImport extends Model
{
    protected $fillable = [
        'user_id',
        'plate_original',
        'plate_new',
        'brand',
        'model',
        'year',
        'engine_cc',
        'power_kw',
        'status',
        'documents',
        'rejection_reason',
    ];

    protected $casts = [
        'documents' => 'array',
        'year' => 'integer',
        'engine_cc' => 'integer',
        'power_kw' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
