<?php

namespace App\Modules\Marketplace\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Marketplace\Models\MarketplaceFavorite;
use App\Modules\Marketplace\Models\MarketplaceListing;
use App\Modules\Vehicle\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class MarketplaceController extends Controller
{
    public function index(Request $request): Response
    {
        $query = MarketplaceListing::query()
            ->with(['user', 'vehicle'])
            ->active()
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->has('search')) {
            $query->where('title', 'like', "%{$request->search}%")
                ->orWhere('description', 'like', "%{$request->search}%");
        }

        if ($request->has('brand')) {
            $query->whereHas('vehicle', function ($q) use ($request) {
                $q->where('brand', $request->brand);
            });
        }

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->has('year_from')) {
            $query->whereHas('vehicle', function ($q) use ($request) {
                $q->where('year', '>=', $request->year_from);
            });
        }

        if ($request->has('year_to')) {
            $query->whereHas('vehicle', function ($q) use ($request) {
                $q->where('year', '<=', $request->year_to);
            });
        }

        if ($request->has('location')) {
            $query->where('location_city', 'like', "%{$request->location}%")
                ->orWhere('location_region', 'like', "%{$request->location}%");
        }

        $listings = $query->paginate(12);

        // Add favorite status for authenticated user
        if (auth()->check()) {
            $userFavorites = MarketplaceFavorite::where('user_id', auth()->id())
                ->pluck('listing_id')
                ->toArray();

            $listings->getCollection()->transform(function ($listing) use ($userFavorites) {
                $listing->is_favorite = in_array($listing->id, $userFavorites);

                return $listing;
            });
        }

        return Inertia::render('Marketplace/Index', [
            'listings' => $listings,
            'filters' => $request->only(['search', 'brand', 'min_price', 'max_price', 'year_from', 'year_to', 'location']),
        ]);
    }

    public function show(MarketplaceListing $listing): Response
    {
        $listing->load(['user', 'vehicle']);
        $listing->increment('views');

        $isFavorite = auth()->check() && $listing->isFavoritedBy(auth()->user());

        return Inertia::render('Listings/Show', [
            'listing' => array_merge($listing->toArray(), ['is_favorite' => $isFavorite]),
        ]);
    }

    public function create(): Response
    {
        $vehicles = Vehicle::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Listings/Create', [
            'vehicles' => $vehicles,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'phone' => ['required', 'string'],
        ]);

        $listing = MarketplaceListing::create([
            ...$validated,
            'user_id' => auth()->id(),
            'status' => 'active',
            'expires_at' => now()->addDays(30),
        ]);

        // Upload photos
        $photos = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store("marketplace-photos/{$listing->id}", 'public');
                $photos[] = Storage::url($path);
            }
            $listing->photos = $photos;
            $listing->save();
        }

        return redirect()->route('marketplace.show', $listing)
            ->with('success', 'Anuncio publicado correctamente');
    }

    public function update(Request $request, MarketplaceListing $listing)
    {
        $this->authorize('update', $listing);

        $validated = $Request->validate([
            'title' => ['sometimes', 'string', 'max:150'],
            'description' => ['sometimes', 'string', 'min:50'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'is_negotiable' => ['sometimes', 'boolean'],
            'status' => ['sometimes', 'in:draft,active,sold,reserved,expired'],
        ]);

        $listing->update($validated);

        return back()->with('success', 'Anuncio actualizado correctamente');
    }

    public function destroy(MarketplaceListing $listing)
    {
        $this->authorize('delete', $listing);

        $listing->delete();

        return redirect()->route('marketplace.index')
            ->with('success', 'Anuncio eliminado correctamente');
    }

    public function toggleFavorite(MarketplaceListing $listing)
    {
        if (! auth()->check()) {
            return back()->with('error', 'Debes iniciar sesión para guardar favoritos');
        }

        $existing = MarketplaceFavorite::where('user_id', auth()->id())
            ->where('listing_id', $listing->id)
            ->first();

        if ($existing) {
            $existing->delete();

            return back()->with('success', 'Eliminado de favoritos');
        } else {
            MarketplaceFavorite::create([
                'user_id' => auth()->id(),
                'listing_id' => $listing->id,
            ]);

            return back()->with('success', 'Añadido a favoritos');
        }
    }

    public function myListings(): Response
    {
        $listings = MarketplaceListing::where('user_id', auth()->id())
            ->with(['vehicle'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Marketplace/MyListings', [
            'listings' => $listings,
        ]);
    }

    public function createPayment(MarketplaceListing $listing)
    {
        $this->authorize('purchase', $listing);

        $action = new \App\Modules\Billing\Actions\CreatePaymentIntentAction(
            app(\Stripe\StripeClient::class)
        );

        $intent = $action->execute(
            (float) $listing->price,
            $listing->currency,
            "Compra {$listing->title}",
            $listing->user_id,
            ['listing_id' => $listing->id]
        );

        return response()->json([
            'client_secret' => $intent->stripe_payment_intent_id,
            'amount' => $intent->amount,
            'currency' => $intent->currency,
        ]);
    }
}
