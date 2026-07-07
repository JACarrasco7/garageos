<?php

namespace App\Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Billing\Models\StripeConfig;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminController extends Controller
{
    public function dashboard(): Response
    {
        $this->authorize('view', 'admin.dashboard');

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'users' => User::count(),
                'roles' => Role::count(),
                'permissions' => Permission::count(),
            ],
        ]);
    }

    public function stripeKeys(Request $request): Response
    {
        $this->authorize('manage stripe keys');

        $config = StripeConfig::first();

        return Inertia::render('Admin/StripeKeys', [
            'stripeKey' => $config?->key ?? config('services.stripe.key'),
            'stripeSecret' => $config?->secret ?? config('services.stripe.secret'),
            'stripeWebhookSecret' => $config?->webhook_secret ?? config('services.stripe.webhook.secret'),
        ]);
    }

    public function updateStripeKeys(Request $request)
    {
        $this->authorize('manage stripe keys');

        $validated = $request->validate([
            'stripe_key' => ['required', 'string'],
            'stripe_secret' => ['required', 'string'],
            'stripe_webhook_secret' => ['nullable', 'string'],
        ]);

        StripeConfig::updateKeys([
            'key' => $validated['stripe_key'],
            'secret' => $validated['stripe_secret'],
            'webhook_secret' => $validated['stripe_webhook_secret'] ?? null,
        ]);

        return back()->with('success', 'Claves de Stripe actualizadas correctamente');
    }
}
