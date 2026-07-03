<?php

namespace App\Modules\Vehicle\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Vehicle\Models\Vehicle;
use App\Modules\Vehicle\Models\VehiclePhoto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class VehiclePhotoController extends Controller
{
    public function index(Vehicle $vehicle): Response
    {
        Gate::authorize('view', $vehicle->garage);

        $photos = $vehicle->photos()
            ->orderBy('sort_order')
            ->orderBy('created_at')
            ->get();

        $categories = collect([
            ['value' => 'principal', 'label' => 'Principal'],
            ['value' => 'frontal', 'label' => 'Frontal'],
            ['value' => 'lateral', 'label' => 'Lateral'],
            ['value' => 'trasero', 'label' => 'Trasero'],
            ['value' => 'interior', 'label' => 'Interior'],
            ['value' => 'motor', 'label' => 'Motor'],
            ['value' => 'averia', 'label' => 'Avería'],
            ['value' => 'daño', 'label' => 'Daño'],
            ['value' => 'documento', 'label' => 'Documento'],
            ['value' => 'antes_reparacion', 'label' => 'Antes reparación'],
            ['value' => 'despues_reparacion', 'label' => 'Después reparación'],
        ]);

        return Inertia::render('Vehicle/VehiclePhotos', [
            'vehicle' => $vehicle,
            'photos' => $photos,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request, Vehicle $vehicle): JsonResponse
    {
        Gate::authorize('update', $vehicle->garage);

        $validated = $request->validate([
            'files' => 'required|array|max:10',
            'files.*' => 'required|image|max:10240',
            'category' => 'required|in:principal,frontal,lateral,trasero,interior,motor,averia,daño,documento,antes_reparacion,despues_reparacion',
            'caption' => 'nullable|string|max:200',
        ]);

        $photos = collect();
        $maxSortOrder = $vehicle->photos()->max('sort_order') ?? 0;

        foreach ($validated['files'] as $index => $file) {
            $path = $file->store("vehicles/{$vehicle->id}/photos", 'public');

            $photo = $vehicle->photos()->create([
                'file_path' => $path,
                'category' => $validated['category'],
                'caption' => $validated['caption'] ?? null,
                'sort_order' => $maxSortOrder + $index + 1,
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);

            $photos->push($photo);
        }

        return response()->json($photos);
    }

    public function update(Request $request, Vehicle $vehicle, VehiclePhoto $photo): JsonResponse
    {
        Gate::authorize('update', $vehicle->garage);

        if ($photo->vehicle_id !== $vehicle->id) {
            return response()->json(['error' => 'Photo not found'], 404);
        }

        $validated = $request->validate([
            'category' => 'sometimes|in:principal,frontal,lateral,trasero,interior,motor,averia,daño,documento,antes_reparacion,despues_reparacion',
            'caption' => 'sometimes|nullable|string|max:200',
            'sort_order' => 'sometimes|integer|min:0',
        ]);

        $photo->update($validated);

        return response()->json($photo);
    }

    public function reorder(Request $request, Vehicle $vehicle): JsonResponse
    {
        Gate::authorize('update', $vehicle->garage);

        $validated = $request->validate([
            'photo_ids' => 'required|array',
            'photo_ids.*' => 'required|integer|exists:vehicle_photos,id',
        ]);

        foreach ($validated['photo_ids'] as $index => $photoId) {
            $photo = $vehicle->photos()->find($photoId);
            if ($photo) {
                $photo->update(['sort_order' => $index + 1]);
            }
        }

        return response()->json(['success' => true]);
    }

    public function destroy(Vehicle $vehicle, VehiclePhoto $photo): JsonResponse
    {
        Gate::authorize('update', $vehicle->garage);

        if ($photo->vehicle_id !== $vehicle->id) {
            return response()->json(['error' => 'Photo not found'], 404);
        }

        Storage::disk('public')->delete($photo->file_path);

        $photo->delete();

        return response()->json(['success' => true]);
    }
}
