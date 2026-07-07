<?php

namespace App\Modules\VehicleImport\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_import_id',
        'vin_verified',
        'ownership_verified',
        'technical_data_verified',
        'itv_verified',
        'legal_status_verified',
        'verified_by',
        'verified_at',
        'overall_status',
        'notes',
        'report_pdf_path',
    ];

    protected $casts = [
        'vin_verified' => 'boolean',
        'ownership_verified' => 'boolean',
        'technical_data_verified' => 'boolean',
        'itv_verified' => 'boolean',
        'legal_status_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function vehicleImport(): BelongsTo
    {
        return $this->belongsTo(VehicleImport::class);
    }

    public function auditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
