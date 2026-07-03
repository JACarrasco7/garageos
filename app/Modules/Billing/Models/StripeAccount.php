<?php

namespace App\Modules\Billing\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StripeAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stripe_account_id',
        'charges_enabled',
        'payouts_enabled',
        'country',
        'business_type',
        'business_profile',
        'onboarding_completed',
        'tos_acceptance_date',
    ];

    protected $casts = [
        'charges_enabled' => 'boolean',
        'payouts_enabled' => 'boolean',
        'onboarding_completed' => 'boolean',
        'tos_acceptance_date' => 'datetime',
        'business_profile' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('charges_enabled', true)
            ->where('payouts_enabled', true);
    }

    public function scopeCompletedOnboarding($query)
    {
        return $query->where('onboarding_completed', true);
    }
}
