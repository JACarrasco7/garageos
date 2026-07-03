<?php

namespace App\Modules\Listings\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Listings\Models\Listing;
use App\Modules\Listings\Services\ListingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ListingController extends Controller
{
    public function __construct(protected ListingService $service) {}

    /**
     * Display a listing index.
     */
    public function index(): Response
    {
        return Inertia::render('Listings/Index');
    }

    /**
     * Full-text search API endpoint.
     */
    public function search(Request $request): JsonResponse
    {
        $query = trim((string) $request->get('q', ''));

        if ($query === '') {
            return response()->json([
                'data' => [],
                'meta' => ['total' => 0, 'query' => ''],
            ]);
        }

        $results = $this->service->search($query, 15);

        return response()->json([
            'data' => $results->items(),
            'meta' => [
                'total' => $results->total(),
                'per_page' => $results->perPage(),
                'current_page' => $results->currentPage(),
                'query' => $query,
            ],
        ]);
    }

    /**
     * Nearby listings API endpoint.
     */
    public function nearby(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'radius' => ['nullable', 'integer', 'min:1', 'max:500'],
        ]);

        $radius = $validated['radius'] ?? 25;

        $listings = Listing::where('is_active', true)
            ->nearby($validated['lat'], $validated['lng'], $radius)
            ->limit(50)
            ->get()
            ->map(fn ($l) => [
                'id' => $l->id,
                'title' => $l->title,
                'brand' => $l->brand,
                'model' => $l->model,
                'price_eur' => (float) $l->price_eur,
                'lat' => (float) $l->lat,
                'lng' => (float) $l->lng,
                'year' => $l->year,
                'mileage_km' => $l->mileage_km,
            ]);

        return response()->json([
            'data' => $listings,
            'meta' => [
                'lat' => $validated['lat'],
                'lng' => $validated['lng'],
                'radius_km' => $radius,
                'count' => $listings->count(),
            ],
        ]);
    }
}
