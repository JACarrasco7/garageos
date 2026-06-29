<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Cashier\Checkout;

class SubscriptionController extends Controller
{
    public function index()
    {
        return Inertia::render('Subscription/Index', [
            'plans' => [
                ['id' => 'basic', 'name' => 'Básico', 'price' => 4.99, 'features' => ['Hasta 3 vehículos', 'Alertas básicas']],
                ['id' => 'pro', 'name' => 'Pro', 'price' => 9.99, 'features' => ['Vehículos ilimitados', 'Alertas + OCR', 'Informes PDF']],
            ],
        ]);
    }

    public function checkout(Request $request, string $plan)
    {
        return $request->user()->checkoutRedirect($plan, [
            'success_url' => route('subscription.success'),
            'cancel_url' => route('subscription.cancel'),
        ]);
    }

    public function success()
    {
        return Inertia::render('Subscription/Success');
    }
}
