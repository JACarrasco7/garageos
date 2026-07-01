<?php

namespace App\Modules\Maintenance\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Maintenance\Models\Workshop;
use App\Modules\Maintenance\Models\WorkshopReview;
use Inertia\Inertia;
use Inertia\Response;

class PublicWorkshopController extends Controller
{
    public function index(): Response
    {
        $workshops = Workshop::where('is_verified', true)
            ->withAvg('reviews', 'rating')
            ->orderByDesc('rating')
            ->paginate(12);

        return Inertia::render('Workshop/Index', [
            'workshops' => $workshops,
        ]);
    }

    public function show(Workshop $workshop): Response
    {
        $workshop->load([
            'reviews' => fn($q) => $q->latest()->limit(10),
            'maintenanceEntries' => fn($q) => $q->limit(5),
        ]);

        return Inertia::render('Workshop/Show', [
            'workshop' => $workshop,
            'reviews' => $workshop->reviews()->limit(10)->get(),
        ]);
    }

    public function review(Workshop $workshop): \Illuminate\Http\RedirectResponse
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