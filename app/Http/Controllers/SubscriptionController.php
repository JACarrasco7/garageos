<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $plans = collect(config('subscription.plans'))->map(function ($plan, $key) {
            return array_merge($plan, ['id' => $key]);
        })->values()->all();

        return Inertia::render('Subscription/Index', [
            'plans' => $plans,
            'subscribed' => $user->subscribed('default'),
            'subscription' => $user->subscription('default'),
        ]);
    }

    public function checkout(Request $request, string $plan)
    {
        $planConfig = config("subscription.plans.{$plan}");

        if (! $planConfig) {
            abort(404, 'Plan no encontrado');
        }

        $user = $request->user();

        if ($user->subscribed('default')) {
            return redirect()->route('subscription.index')
                ->with('info', 'Ya tienes una suscripción activa.');
        }

        $stripePriceId = $planConfig['stripe_price_id'] ?? null;

        if (! $stripePriceId || $stripePriceId === 'price_basic_placeholder' || $stripePriceId === 'price_pro_placeholder') {
            return redirect()->route('subscription.index')
                ->with('error', 'Las suscripciones no están configuradas aún. Contacta al administrador.');
        }

        return $user->newSubscription('default', $stripePriceId)
            ->checkout([
                'success_url' => route('subscription.success').'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('subscription.index'),
            ]);
    }

    public function success(Request $request)
    {
        return Inertia::render('Subscription/Success');
    }

    public function cancel(Request $request)
    {
        $subscription = $request->user()->subscription('default');

        if ($subscription && $subscription->active()) {
            $subscription->cancel();
        }

        return redirect()->route('subscription.index')
            ->with('success', 'Suscripción cancelada. Seguirá activa hasta el fin del período.');
    }

    public function resume(Request $request)
    {
        $subscription = $request->user()->subscription('default');

        if ($subscription && $subscription->onGracePeriod()) {
            $subscription->resume();
        }

        return redirect()->route('subscription.index')
            ->with('success', 'Suscripción reactivada.');
    }
}
