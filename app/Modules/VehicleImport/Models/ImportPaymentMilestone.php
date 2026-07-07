<?php

namespace App\Modules\VehicleImport\Models;

use App\Modules\Billing\Models\PaymentIntent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportPaymentMilestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_import_id',
        'payment_intent_id',
        'milestone',
        'amount',
        'status',
        'released_at',
        'release_condition',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'released_at' => 'datetime',
    ];

    public function vehicleImport(): BelongsTo
    {
        return $this->belongsTo(VehicleImport::class);
    }

    public function paymentIntent(): BelongsTo
    {
        return $this->belongsTo(PaymentIntent::class, 'payment_intent_id');
    }
}
