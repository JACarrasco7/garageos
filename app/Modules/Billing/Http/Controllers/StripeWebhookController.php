<?php

namespace App\Modules\Billing\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\StripeClient;

class StripeWebhookController extends Controller
{
    public function __construct(private StripeClient $stripe) {}

    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('stripe-signature');
        $secret = config('services.stripe.webhook.secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe webhook: Invalid payload', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Stripe webhook: Invalid signature', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        match ($event->type) {
            'payment_intent.succeeded' => $this->handlePaymentSucceeded($event->data->object),
            'payment_intent.payment_failed' => $this->handlePaymentFailed($event->data->object),
            'account.updated' => $this->handleAccountUpdated($event->data->object),
            'charge.refunded' => $this->handleChargeRefunded($event->data->object),
            default => null,
        };

        return response()->json(['received' => true]);
    }

    private function handlePaymentSucceeded($paymentIntent): void
    {
        $localIntent = \App\Modules\Billing\Models\PaymentIntent::where(
            'stripe_payment_intent_id',
            $paymentIntent->id
        )->first();

        if ($localIntent) {
            $localIntent->update(['status' => 'succeeded']);
        }
    }

    private function handlePaymentFailed($paymentIntent): void
    {
        $localIntent = \App\Modules\Billing\Models\PaymentIntent::where(
            'stripe_payment_intent_id',
            $paymentIntent->id
        )->first();

        if ($localIntent) {
            $localIntent->update(['status' => 'failed']);
        }
    }

    private function handleAccountUpdated($account): void
    {
        $localAccount = \App\Modules\Billing\Models\StripeAccount::where(
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
}