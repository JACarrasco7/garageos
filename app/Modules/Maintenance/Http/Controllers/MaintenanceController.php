<?php

namespace App\Modules\Maintenance\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Maintenance\Models\MaintenanceEntry;
use App\Modules\Vehicle\Models\Vehicle;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MaintenanceController extends Controller
{
    public function index(Vehicle $vehicle): Response
    {
        $this->authorize('view', $vehicle->garage);

        $entries = $vehicle->maintenanceEntries()
            ->with('workshop')
            ->orderByDesc('service_date')
            ->get();

        $totalCost = $entries->sum('cost');
        $costPerKm = $vehicle->current_km > 0 ? round($totalCost / $vehicle->current_km, 2) : 0;

        return Inertia::render('Maintenance/Index', [
            'vehicle' => $vehicle,
            'entries' => $entries,
            'stats' => [
                'total_cost' => $totalCost,
                'cost_per_km' => $costPerKm,
                'entries_count' => $entries->count(),
            ],
        ]);
    }

    public function create(Vehicle $vehicle): Response
    {
        $this->authorize('update', $vehicle->garage);
        return Inertia::render('Maintenance/Create', ['vehicle' => $vehicle]);
    }

    public function store(Request $request, Vehicle $vehicle): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('update', $vehicle->garage);

        $validated = $request->validate([
            'type' => ['required', 'in:aceite,filtros,neumaticos,frenos,distribucion,embrague,bateria,itv,revision_general,otro'],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'km_at_service' => ['required', 'integer', 'min:0'],
            'service_date' => ['required', 'date'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $vehicle->maintenanceEntries()->create($validated);

        if ($validated['km_at_service'] > $vehicle->current_km) {
            $vehicle->update(['current_km' => $validated['km_at_service']]);
        }

        return back()->with('success', 'Entrada de mantenimiento añadida');
    }
}