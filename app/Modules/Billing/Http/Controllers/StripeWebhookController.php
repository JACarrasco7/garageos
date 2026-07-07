<?php

namespace App\Modules\Billing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Billing\Models\PaymentIntent;
use App\Modules\Billing\Models\PlatformFee;
use App\Modules\Billing\Models\StripeAccount;
use App\Modules\Billing\Models\StripeConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function __construct(private StripeClient $stripe) {}

    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('stripe-signature');
        $secret = StripeConfig::getWebhookSecret();

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe webhook: Invalid payload', ['error' => $e->getMessage()]);

            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook: Invalid signature', ['error' => $e->getMessage()]);

            return response()->json(['error' => 'Invalid signature'], 400);
        }

        match ($event->type) {
            'payment_intent.created' => $this->handlePaymentCreated($event->data->object),
            'payment_intent.succeeded' => $this->handlePaymentSucceeded($event->data->object),
            'payment_intent.payment_failed' => $this->handlePaymentFailed($event->data->object),
            'account.updated' => $this->handleAccountUpdated($event->data->object),
            'charge.refunded' => $this->handleChargeRefunded($event->data->object),
            default => null,
        };

        return response()->json(['received' => true]);
    }

    private function handlePaymentCreated($paymentIntent): void
    {
        if (!PaymentIntent::where('stripe_payment_intent_id', $paymentIntent->id)->exists()) {
            PaymentIntent::create([
                'stripe_payment_intent_id' => $paymentIntent->id,
                'amount' => $paymentIntent->amount / 100,
                'currency' => $paymentIntent->currency,
                'status' => $paymentIntent->status,
                'description' => $paymentIntent->description ?? null,
                'metadata' => $paymentIntent->metadata?->toArray() ?? [],
            ]);
        }
    }

    private function handlePaymentSucceeded($paymentIntent): void
    {
        $localIntent = PaymentIntent::where(
            'stripe_payment_intent_id',
            $paymentIntent->id
        )->first();

        if ($localIntent) {
            $localIntent->update([
                'status' => 'succeeded',
                'metadata' => array_merge($localIntent->metadata, $paymentIntent->metadata?->toArray() ?? []),
            ]);

            if ($localIntent->connected_account_id) {
                try {
                    $this->createPlatformFee($localIntent);
                } catch (\Exception $e) {
                    Log::error('Failed to create platform fee', [
                        'payment_intent_id' => $localIntent->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    private function handlePaymentFailed($paymentIntent): void
    {
        $localIntent = PaymentIntent::where(
            'stripe_payment_intent_id',
            $paymentIntent->id
        )->first();

        if ($localIntent) {
            $localIntent->update([
                'status' => 'failed',
                'metadata' => array_merge($localIntent->metadata, [
                    'last_payment_error' => $paymentIntent->last_payment_error?->message ?? 'Unknown error',
                ]),
            ]);
        }
    }

    private function handleAccountUpdated($account): void
    {
        $localAccount = StripeAccount::where(
            'stripe_account_id',
            $account->id
        )->first();

        if ($localAccount) {
            $localAccount->update([
                'charges_enabled' => $account->charges_enabled,
                'payouts_enabled' => $account->payouts_enabled,
                'onboarding_completed' => $account->charges_enabled && $account->payouts_enabled,
            ]);
        }
    }

    private function handleChargeRefunded($charge): void
    {
        Log::info('Charge refunded', ['charge_id' => $charge->id]);
    }

    private function createPlatformFee(PaymentIntent $paymentIntent): void
    {
        $existingFee = PlatformFee::where('payment_intent_id', $paymentIntent->id)->first();

        if ($existingFee) {
            return;
        }

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
