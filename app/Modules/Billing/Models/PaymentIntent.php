<?php

namespace App\Modules\Billing\Models;

use App\Models\User;
use App\Modules\VehicleImport\Models\VehicleImport;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentIntent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vehicle_import_id',
        'stripe_payment_intent_id',
        'amount',
        'currency',
        'status',
        'description',
        'metadata',
        'platform_fee_amount',
        'platform_fee_percent',
        'connected_account_id',
        'application_fee_amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'platform_fee_amount' => 'decimal:2',
        'platform_fee_percent' => 'decimal:2',
        'application_fee_amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicleImport(): BelongsTo
    {
        return $this->belongsTo(VehicleImport::class, 'vehicle_import_id');
    }

    public function stripeAccount(): BelongsTo
    {
        return $this->belongsTo(StripeAccount::class, 'connected_account_id', 'stripe_account_id');
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'succeeded');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['requires_payment_method', 'requires_confirmation', 'requires_action', 'processing']);
    }

    public function scopeWithVehicleImport($query)
    {
        return $query->whereNotNull('vehicle_import_id');
    }
}
