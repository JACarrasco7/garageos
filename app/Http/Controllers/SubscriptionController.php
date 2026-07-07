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

        // Si es el plan gratuito, activar directamente
        if ($plan === 'free') {
            // Asegurar que el usuario tenga el rol 'user'
            if (! $user->hasRole('user')) {
                $user->assignRole('user');
            }

            return redirect()->route('subscription.index')
                ->with('success', 'Plan gratuito activado correctamente.');
        }

        // Si ya tiene suscripción activa, no crear otra
        if ($user->subscribed('default')) {
            return redirect()->route('subscription.index')
                ->with('info', 'Ya tienes una suscripción activa.');
        }

        $stripePriceId = $planConfig['stripe_price_id'] ?? null;

        if (! $stripePriceId || str_contains($stripePriceId, 'placeholder')) {
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
        $user = $request->user();

        // Asignar rol según el plan suscrito
        $plan = $user->subscription('default')?->stripe_price;
        if ($plan === 'importer') {
            $user->assignRole('importer');
        }

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
