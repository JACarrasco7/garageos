<?php

namespace App\Modules\Listings\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Listings\Models\Listing;
use App\Modules\Listings\Services\ListingService;
use App\Modules\Vehicle\Models\Vehicle;
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
        $listings = Listing::query()
            ->where('is_active', true)
            ->latest()
            ->get()
            ->map(fn ($l) => [
                'id' => $l->id,
                'title' => $l->title,
                'brand' => $l->brand,
                'model' => $l->model,
                'year' => $l->year,
                'price_eur' => (float) $l->price_eur,
                'mileage_km' => $l->mileage_km,
            ]);

        return Inertia::render('Listings/Index', [
            'listings' => $listings,
        ]);
    }

    /**
     * Show the form to create a new listing.
     */
    public function create(): Response
    {
        $vehicles = auth()->user()->garages()->with('vehicles')->get()->flatMap->vehicles;

        return Inertia::render('Listings/Create', [
            'vehicles' => $vehicles->map(fn ($v) => [
                'id' => $v->id,
                'plate' => $v->plate,
                'brand' => $v->brand,
                'model' => $v->model,
                'year' => $v->year,
                'mileage_km' => $v->current_km,
                'fuel_type' => $v->fuel_type,
                'power_hp' => $v->power_hp,
                'gearbox' => $v->gearbox,
                'photos' => $v->photos()->pluck('url')->toArray(),
            ])->values(),
        ]);
    }

    /**
     * Store a newly created listing.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'title' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'is_negotiable' => ['boolean'],
            'location_city' => ['required', 'string'],
            'location_region' => ['required', 'string'],
            'photos' => ['array', 'max:10'],
        ]);

        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);

        $listing = Listing::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'brand' => $vehicle->brand,
            'model' => $vehicle->model,
            'year' => $vehicle->year,
            'mileage_km' => $vehicle->current_km,
            'fuel_type' => $vehicle->fuel_type,
            'power_hp' => $vehicle->power_hp,
            'gearbox' => $vehicle->gearbox,
            'price_eur' => $validated['price'],
            'currency' => $validated['currency'],
            'is_negotiable' => $validated['is_negotiable'],
            'created_by_user_id' => auth()->id(),
            'is_active' => true,
        ]);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $listing->addMedia($photo)->toMediaCollection('photos');
            }
        }

        return redirect()->route('listings.show', $listing->id)
            ->with('success', 'Anuncio publicado correctamente.');
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
