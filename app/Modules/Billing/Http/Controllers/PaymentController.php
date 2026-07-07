<?php

namespace App\Modules\Billing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Billing\Models\PaymentIntent;
use App\Modules\Billing\Models\StripeConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\StripeClient;

class PaymentController extends Controller
{
    protected StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(StripeConfig::getSecret());
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', 'string', 'size:3'],
            'description' => ['required', 'string', 'max:500'],
            'connected_account_id' => ['nullable', 'string'],
            'platform_fee_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $platformFeePercent = $validated['platform_fee_percent'] ?? 8.00;
        $platformFeeAmount = round($validated['amount'] * ($platformFeePercent / 100), 2);
        $applicationFeeAmount = $platformFeeAmount;

        $paymentIntentParams = [
            'amount' => (int) round($validated['amount'] * 100),
            'currency' => $validated['currency'],
            'description' => $validated['description'],
            'metadata' => [
                'user_id' => Auth::id(),
            ],
        ];

        if (! empty($validated['connected_account_id'])) {
            $paymentIntentParams['transfer_data'] = [
                'destination' => $validated['connected_account_id'],
            ];
            $paymentIntentParams['application_fee_amount'] = (int) round($applicationFeeAmount * 100);
        }

        $stripePaymentIntent = $this->stripe->paymentIntents->create($paymentIntentParams);

        $paymentIntent = PaymentIntent::create([
            'user_id' => Auth::id(),
            'stripe_payment_intent_id' => $stripePaymentIntent->id,
            'amount' => $validated['amount'],
            'currency' => $validated['currency'],
            'status' => $stripePaymentIntent->status,
            'description' => $validated['description'],
            'metadata' => $stripePaymentIntent->metadata->toArray(),
            'platform_fee_amount' => $platformFeeAmount,
            'platform_fee_percent' => $platformFeePercent,
            'connected_account_id' => $validated['connected_account_id'],
            'application_fee_amount' => $applicationFeeAmount,
        ]);

        return response()->json([
            'payment_intent' => $paymentIntent,
            'client_secret' => $stripePaymentIntent->client_secret,
        ]);
    }
}
