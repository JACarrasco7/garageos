<?php

namespace App\Modules\Maintenance\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Maintenance\Models\Workshop;
use App\Modules\Maintenance\Models\WorkshopReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicWorkshopController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Workshop::where('is_verified', true)
            ->withAvg('reviews', 'rating');

        if ($request->filled(['lat', 'lng'])) {
            $lat = (float) $request->get('lat');
            $lng = (float) $request->get('lng');
            $radius = (int) $request->get('radius', 25);
            $query->nearby($lat, $lng, $radius);
        } else {
            $query->orderByDesc('rating');
        }

        $workshops = $query->paginate(12)->withQueryString();

        return Inertia::render('Workshop/Index', [
            'workshops' => $workshops,
            'filters' => $request->only(['lat', 'lng', 'radius']),
        ]);
    }

    public function nearby(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'radius' => ['nullable', 'integer', 'min:1', 'max:500'],
        ]);

        $radius = $validated['radius'] ?? 25;

        $workshops = Workshop::where('is_verified', true)
            ->nearby($validated['lat'], $validated['lng'], $radius)
            ->withAvg('reviews', 'rating')
            ->limit(50)
            ->get()
            ->map(fn ($w) => [
                'id' => $w->id,
                'name' => $w->name,
                'address' => $w->address,
                'city' => $w->city,
                'lat' => (float) $w->lat,
                'lng' => (float) $w->lng,
                'rating' => (float) $w->rating,
                'reviews_avg' => $w->reviews_avg_rating,
                'distance_km' => $w->distanceFrom($validated['lat'], $validated['lng']),
            ]);

        return response()->json([
            'data' => $workshops,
            'meta' => [
                'lat' => $validated['lat'],
                'lng' => $validated['lng'],
                'radius_km' => $radius,
                'count' => $workshops->count(),
            ],
        ]);
    }

    public function show(Workshop $workshop): Response
    {
        $workshop->load([
            'reviews' => fn ($q) => $q->latest()->limit(10),
            'maintenanceEntries' => fn ($q) => $q->limit(5),
        ]);

        return Inertia::render('Workshop/Show', [
            'workshop' => $workshop,
            'reviews' => $workshop->reviews()->limit(10)->get(),
        ]);
    }

    public function review(Workshop $workshop): RedirectResponse
    {
        $validated = request()->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:500'],
        ]);

        // Evitar reseñas duplicadas del mismo usuario
        $existingReview = WorkshopReview::where('workshop_id', $workshop->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existingReview) {
            $existingReview->update($validated);
        } else {
            WorkshopReview::create([
                'workshop_id' => $workshop->id,
                'user_id' => auth()->id(),
                ...$validated,
            ]);
        }

        // Actualizar rating promedio
        $avgRating = $workshop->reviews()->avg('rating');
        $workshop->update(['rating' => round($avgRating)]);

        return back()->with('success', 'Reseña enviada correctamente');
    }
}
