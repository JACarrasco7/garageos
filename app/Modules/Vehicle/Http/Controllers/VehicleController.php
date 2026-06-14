<?php

namespace App\Modules\Vehicle\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Vehicle\Models\Vehicle;
use App\Modules\Identity\Models\Garage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VehicleController extends Controller
{
    public function index(): Response
    {
        $vehicles = Vehicle::with(['specs', 'garage'])
            ->whereHas('garage', fn($q) => $q->where('user_id', auth()->id()))
            ->latest()
            ->get();

        return Inertia::render('Vehicle/Index', [
            'vehicles' => $vehicles,
        ]);
    }

    public function create(): Response
    {
        $garages = Garage::where('user_id', auth()->id())->get(['id', 'name']);

        return Inertia::render('Vehicle/Create', [
            'garages' => $garages,
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'garage_id' => ['required', 'exists:garages,id', fn($q) => $q->where('user_id', auth()->id())],
            'plate' => ['required', 'string', 'max:10'],
            'vin' => ['nullable', 'string', 'max:17', 'unique:vehicles'],
            'brand' => ['required', 'string', 'max:50'],
            'model' => ['required', 'string', 'max:80'],
            'year' => ['required', 'integer', 'min:1900', 'max:' . now()->year + 1],
            'fuel_type' => ['required', 'in:gasolina,diesel,hibrido,electrico,glp'],
            'color' => ['nullable', 'string', 'max:40'],
            'current_km' => ['required', 'integer', 'min:0'],
        ]);

        $vehicle = Vehicle::create($validated);

        // Disparar evento para crear alertas por defecto
        event(new \App\Modules\Vehicle\Events\VehicleRegistered($vehicle));

        return redirect()->route('vehicles.show', $vehicle)->with('success', 'Vehículo creado correctamente');
    }

    public function show(Vehicle $vehicle): Response
    {
        $this->authorize('view', $vehicle->garage);

        $vehicle->load([
            'specs',
            'documents' => fn($q) => $q->latest()->limit(10),
            'maintenanceEntries' => fn($q) => $q->latest()->limit(10),
            'alertRules' => fn($q) => $q->where('is_active', true),
        ]);

        return Inertia::render('Vehicle/Show', [
            'vehicle' => $vehicle,
        ]);
    }
}