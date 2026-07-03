<?php

namespace App\Modules\Billing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Billing\Models\PaymentIntent;
use App\Modules\Billing\Models\PlatformFee;
use App\Modules\Billing\Models\StripeAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;
use Stripe\Webhook;

class PaymentController extends Controller
{
    public function __construct(private StripeClient $stripe) {}

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

    public function webhook(Request $request)
    {
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        match ($event->type) {
            'payment_intent.succeeded' => $this->handlePaymentSucceeded($event->data->object),
            'payment_intent.payment_failed' => $this->handlePaymentFailed($event->data->object),
            'account.updated' => $this->handleAccountUpdated($event->data->object),
            default => null,
        };

        return response()->json(['status' => 'success']);
    }

    private function handlePaymentSucceeded($stripePaymentIntent)
    {
        $paymentIntent = PaymentIntent::where('stripe_payment_intent_id', $stripePaymentIntent->id)->first();

        if ($paymentIntent) {
            $paymentIntent->update([
                'status' => 'succeeded',
                'metadata' => array_merge($paymentIntent->metadata, $stripePaymentIntent->metadata->toArray()),
            ]);

            if ($paymentIntent->connected_account_id) {
                $this->createPlatformFee($paymentIntent);
            }
        }
    }

    private function handlePaymentFailed($stripePaymentIntent)
    {
        $paymentIntent = PaymentIntent::where('stripe_payment_intent_id', $stripePaymentIntent->id)->first();

        if ($paymentIntent) {
            $paymentIntent->update([
                'status' => 'failed',
                'metadata' => array_merge($paymentIntent->metadata, [
                    'last_payment_error' => $stripePaymentIntent->last_payment_error->message ?? 'Unknown error',
                ]),
            ]);
        }
    }

    private function handleAccountUpdated($stripeAccount)
    {
        $account = StripeAccount::where('stripe_account_id', $stripeAccount->id)->first();

        if ($account) {
            $account->update([
                'charges_enabled' => $stripeAccount->charges_enabled,
                'payouts_enabled' => $stripeAccount->payouts_enabled,
                'onboarding_completed' => $stripeAccount->charges_enabled && $stripeAccount->payouts_enabled,
                'business_profile' => $stripeAccount->business_profile->toArray(),
            ]);
        }
    }

    private function createPlatformFee($paymentIntent)
    {
        PlatformFee::create([
            'payment_intent_id' => $paymentIntent->id,
            'stripe_account_id' => $paymentIntent->connected_account_id,
            'amount' => $paymentIntent->platform_fee_amount,
            'currency' => $paymentIntent->currency,
            'description' => "Comisión plataforma ({$paymentIntent->platform_fee_percent}%)",
            'metadata' => [
                'payment_intent_id' => $paymentIntent->stripe_payment_intent_id,
            ],
        ]);
    }
}
