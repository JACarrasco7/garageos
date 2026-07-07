<?php

namespace App\Modules\Billing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_intent_id',
        'stripe_account_id',
        'amount',
        'currency',
        'description',
        'metadata',
        'processed_at',
        'invoice_path',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function paymentIntent()
    {
        return $this->belongsTo(PaymentIntent::class);
    }

    public function stripeAccount()
    {
        return $this->belongsTo(StripeAccount::class, 'stripe_account_id', 'stripe_account_id');
    }

    public function scopeByCurrency($query, $currency)
    {
        return $query->where('currency', $currency);
    }

    public function scopeProcessed($query)
    {
        return $query->whereNotNull('processed_at');
    }

    public function scopeUnprocessed($query)
    {
        return $query->whereNull('processed_at');
    }

    public function markAsProcessed(?string $invoicePath = null): void
    {
        $this->update([
            'processed_at' => now(),
            'invoice_path' => $invoicePath,
        ]);
    }
}
