<?php

namespace App\Modules\Billing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Billing\Models\StripeAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Stripe\StripeClient;

class StripeConnectController extends Controller
{
    public function __construct(private StripeClient $stripe) {}

    public function index(): Response
    {
        $account = StripeAccount::where('user_id', Auth::id())->first();

        return Inertia::render('Billing/StripeConnect', [
            'account' => $account,
            'stripeClientId' => config('services.stripe.client_id'),
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'business_type' => ['required', 'in:individual,company'],
            'country' => ['required', 'string', 'size:2'],
        ]);

        $account = $this->stripe->accounts->create([
            'type' => 'express',
            'country' => $request->country,
            'business_type' => $request->business_type,
            'capabilities' => [
                'transfers' => ['requested' => true],
                'card_payments' => ['requested' => true],
            ],
            'business_profile' => [
                'mcc' => '5734', // Computer software stores
                'url' => route('home'),
            ],
        ]);

        StripeAccount::create([
            'user_id' => Auth::id(),
            'stripe_account_id' => $account->id,
            'country' => $account->country,
            'business_type' => $account->business_type,
        ]);

        return redirect()->route('stripe.connect.onboard', [
            'account_id' => $account->id,
        ]);
    }

    public function onboard(Request $request)
    {
        $account = StripeAccount::where('user_id', Auth::id())
            ->where('stripe_account_id', $request->account_id)
            ->firstOrFail();

        $accountLink = $this->stripe->accountLinks->create([
            'account' => $account->stripe_account_id,
            'refresh_url' => route('stripe.connect.refresh'),
            'return_url' => route('stripe.connect.complete'),
            'type' => 'account_onboarding',
        ]);

        return redirect($accountLink->url);
    }

    public function refresh()
    {
        $account = StripeAccount::where('user_id', Auth::id())->firstOrFail();

        $accountLink = $this->stripe->accountLinks->create([
            'account' => $account->stripe_account_id,
            'refresh_url' => route('stripe.connect.refresh'),
            'return_url' => route('stripe.connect.complete'),
            'type' => 'account_onboarding',
        ]);

        return redirect($accountLink->url);
    }

    public function complete()
    {
        $account = StripeAccount::where('user_id', Auth::id())->firstOrFail();

        $stripeAccount = $this->stripe->accounts->retrieve($account->stripe_account_id);

        $account->update([
            'charges_enabled' => $stripeAccount->charges_enabled,
            'payouts_enabled' => $stripeAccount->payouts_enabled,
            'onboarding_completed' => $stripeAccount->charges_enabled && $stripeAccount->payouts_enabled,
            'business_profile' => $stripeAccount->business_profile->toArray(),
        ]);

        return redirect()->route('stripe.connect.index')
            ->with('success', 'Cuenta Stripe conectada correctamente');
    }

    public function dashboard()
    {
        $account = StripeAccount::where('user_id', Auth::id())->firstOrFail();

        $loginLink = $this->stripe->accounts->createLoginLink($account->stripe_account_id);

        return redirect($loginLink->url);
    }
}
