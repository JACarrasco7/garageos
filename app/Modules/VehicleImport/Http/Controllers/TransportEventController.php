<?php

namespace App\Modules\VehicleImport\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\VehicleImport\Models\TransportEvent;
use App\Modules\VehicleImport\Models\VehicleImport;
use App\Notifications\TransportUpdateNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransportEventController extends Controller
{
    /**
     * Store a new transport event.
     */
    public function store(Request $request, VehicleImport $vehicleImport): JsonResponse
    {
        $this->authorize('update', $vehicleImport);

        $validated = $request->validate([
            'status' => ['required', 'in:pickup_scheduled,picked_up,in_transit,customs,delivered'],
            'location' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'note' => ['nullable', 'string'],
            'photo_path' => ['nullable', 'string'],
            'occurred_at' => ['required', 'date'],
        ]);

        $event = TransportEvent::create([
            'vehicle_import_id' => $vehicleImport->id,
            ...$validated,
        ]);

        // Notify client
        $vehicleImport->user->notify(new TransportUpdateNotification($event));

        return response()->json(['event' => $event]);
    }

    /**
     * Get all transport events for an import.
     */
    public function index(VehicleImport $vehicleImport): JsonResponse
    {
        $this->authorize('view', $vehicleImport);

        $events = $vehicleImport->transportEvents()
            ->orderBy('occurred_at', 'desc')
            ->get();

        return response()->json(['events' => $events]);
    }
}
