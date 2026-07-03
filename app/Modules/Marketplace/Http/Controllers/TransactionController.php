<?php

namespace App\Modules\Marketplace\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Marketplace\Models\MarketplaceListing;
use App\Modules\Marketplace\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'listing_id' => ['required', 'exists:marketplace_listings,id'],
            'type' => ['required', 'in:offer,reservation'],
            'amount' => ['required', 'numeric', 'min:0'],
            'message' => ['nullable', 'string', 'max:500'],
        ]);

        $listing = MarketplaceListing::findOrFail($validated['listing_id']);

        if ($listing->user_id === $request->user()->id) {
            return back()->with('error', 'No puedes hacer una oferta a tu propio anuncio');
        }

        $transaction = Transaction::create([
            ...$validated,
            'buyer_id' => $request->user()->id,
            'seller_id' => $listing->user_id,
            'currency' => 'EUR',
            'status' => 'pending',
            'description' => $validated['message'] ?? "{$validated['type']} por {$validated['amount']}€",
        ]);

        return back()->with('success', 'Transacción creada correctamente');
    }

    public function update(Request $request, Transaction $transaction)
    {
        $this->authorize('update', $transaction);

        $validated = $request->validate([
            'status' => ['required', 'in:accepted,rejected,completed,cancelled'],
        ]);

        $transaction->update(['status' => $validated['status']]);

        if ($validated['status'] === 'accepted' && $transaction->type === 'offer') {
            $transaction->listing->update(['status' => 'reserved']);
        }

        return back()->with('success', 'Transacción actualizada');
    }

    public function index(): Response
    {
        $transactions = Transaction::where('buyer_id', auth()->id())
            ->orWhere('seller_id', auth()->id())
            ->with(['listing', 'buyer', 'seller'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Marketplace/Transactions', [
            'transactions' => $transactions,
        ]);
    }
}
