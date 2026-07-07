<?php

namespace App\Modules\Billing\Actions;

use App\Modules\Billing\Models\PaymentIntent;
use App\Modules\Billing\Models\StripeAccount;
use Illuminate\Support\Facades\Auth;
use Stripe\StripeClient;

class CreatePaymentIntentAction
{
    public function __construct(private StripeClient $stripe) {}

    /**
     * Create a payment intent for marketplace transactions.
     */
    public function execute(
        float $amount,
        string $currency,
        string $description,
        int $sellerId,
        array $metadata = [],
        ?int $vehicleImportId = null
    ): PaymentIntent {
        $sellerAccount = StripeAccount::where('user_id', $sellerId)
            ->where('charges_enabled', true)
            ->where('payouts_enabled', true)
            ->first();

        $platformFeePercent = 8.00;
        $platformFeeAmount = round($amount * ($platformFeePercent / 100), 2);

        $params = [
            'amount' => (int) round($amount * 100),
            'currency' => $currency,
            'description' => $description,
            'metadata' => array_merge([
                'buyer_id' => Auth::id(),
                'seller_id' => $sellerId,
                'vehicle_import_id' => $vehicleImportId,
            ], $metadata),
        ];

        if ($sellerAccount) {
            $params['transfer_data'] = [
                'destination' => $sellerAccount->stripe_account_id,
            ];
            $params['application_fee_amount'] = (int) round($platformFeeAmount * 100);
        }

        $stripeIntent = $this->stripe->paymentIntents->create($params);

        return PaymentIntent::create([
            'user_id' => Auth::id(),
            'vehicle_import_id' => $vehicleImportId,
            'stripe_payment_intent_id' => $stripeIntent->id,
            'amount' => $amount,
            'currency' => $currency,
            'status' => $stripeIntent->status,
            'description' => $description,
            'platform_fee_amount' => $platformFeeAmount,
            'platform_fee_percent' => $platformFeePercent,
            'connected_account_id' => $sellerAccount?->stripe_account_id,
            'application_fee_amount' => $sellerAccount ? $platformFeeAmount : 0,
        ]);
    }
}
