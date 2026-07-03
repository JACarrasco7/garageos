<?php

namespace App\Modules\Billing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Billing\Models\PlatformFee;
use App\Modules\Billing\Models\StripeAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $account = StripeAccount::where('user_id', Auth::id())->first();

        if (! $account) {
            return Inertia::render('Billing/Dashboard', [
                'hasAccount' => false,
            ]);
        }

        $totalEarnings = PlatformFee::where('stripe_account_id', $account->stripe_account_id)
            ->where('processed_at', '!=', null)
            ->sum('amount');

        $pendingFees = PlatformFee::where('stripe_account_id', $account->stripe_account_id)
            ->where('processed_at', null)
            ->get();

        $recentTransactions = PlatformFee::where('stripe_account_id', $account->stripe_account_id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return Inertia::render('Billing/Dashboard', [
            'hasAccount' => true,
            'account' => $account,
            'stats' => [
                'totalEarnings' => $totalEarnings,
                'pendingFees' => $pendingFees->sum('amount'),
                'completedFees' => $totalEarnings,
                'transactionCount' => $recentTransactions->count(),
            ],
            'recentTransactions' => $recentTransactions,
        ]);
    }

    public function invoices(Request $request): Response
    {
        $account = StripeAccount::where('user_id', Auth::id())->firstOrFail();

        $fees = PlatformFee::where('stripe_account_id', $account->stripe_account_id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Billing/Invoices', [
            'fees' => $fees,
        ]);
    }
}
