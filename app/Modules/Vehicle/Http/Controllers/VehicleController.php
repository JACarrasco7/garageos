<?php

namespace App\Modules\Vehicle\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Identity\Models\Garage;
use App\Modules\Maintenance\Actions\RecommendServicePackAction;
use App\Modules\Vehicle\Actions\GenerateVehicleReportAction;
use App\Modules\Vehicle\Actions\RegisterVehicleAction;
use App\Modules\Vehicle\Http\Requests\StoreVehicleRequest;
use App\Modules\Vehicle\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class VehicleController extends Controller
{
    public function index(): Response
    {
        $vehicles = Vehicle::with(['specs', 'garage'])
            ->whereHas('garage', fn ($q) => $q->where('user_id', auth()->id()))
            ->latest()
            ->get();

        return Inertia::render('Vehicle/Index', [
            'vehicles' => $vehicles,
        ]);
    }

    public function create(): Response
    {
        return redirect()->route('vehicles.wizard');
    }

    public function wizard(): Response
    {
        $garages = Garage::where('user_id', auth()->id())->get(['id', 'name']);

        return Inertia::render('Vehicle/Wizard', [
            'garages' => $garages,
        ]);
    }

    public function store(StoreVehicleRequest $request, RegisterVehicleAction $action): RedirectResponse
    {
        $vehicle = $action->execute($request->validated());

        return redirect()->route('vehicles.show', $vehicle)
            ->with('success', 'Vehículo registrado correctamente.');
    }

    public function mobileIndex(): Response
    {
        $vehicles = Vehicle::with(['specs', 'garage'])
            ->whereHas('garage', fn ($q) => $q->where('user_id', auth()->id()))
            ->latest()
            ->get();

        return Inertia::render('Mobile/Vehicle/Index', [
            'vehicles' => $vehicles,
        ]);
    }

    public function mobileCreate(): Response
    {
        $garages = Garage::where('user_id', auth()->id())->get(['id', 'name']);

        return Inertia::render('Mobile/Vehicle/Create', [
            'garages' => $garages,
        ]);
    }

    public function mobileWizard(): Response
    {
        $garages = Garage::where('user_id', auth()->id())->get(['id', 'name']);

        return Inertia::render('Mobile/Vehicle/Wizard', [
            'garages' => $garages,
        ]);
    }

    public function mobileShow(Vehicle $vehicle): Response
    {
        Gate::authorize('view', $vehicle->garage);

        $vehicle->load([
            'specs',
            'photos' => fn ($q) => $q->orderBy('sort_order'),
            'documents' => fn ($q) => $q->latest()->limit(10),
            'maintenanceEntries' => fn ($q) => $q->latest()->limit(10),
            'alertRules' => fn ($q) => $q->where('is_active', true),
        ]);

        $recommendedPack = app(RecommendServicePackAction::class)->execute($vehicle);

        return Inertia::render('Mobile/Vehicle/Show', [
            'vehicle' => array_merge($vehicle->toArray(), [
                'recommended_service_pack' => $recommendedPack?->load('affiliateLinks'),
            ]),
        ]);
    }

    public function mobilePhotos(Vehicle $vehicle): Response
    {
        Gate::authorize('view', $vehicle->garage);

        $vehicle->load(['photos' => fn ($q) => $q->orderBy('sort_order')]);

        return Inertia::render('Mobile/Vehicle/VehiclePhotos', [
            'vehicle' => $vehicle,
        ]);
    }

    public function show(Vehicle $vehicle): Response
    {
        Gate::authorize('view', $vehicle->garage);

        $vehicle->load([
            'specs',
            'photos',
            'documents' => fn ($q) => $q->latest()->limit(10),
            'maintenanceEntries' => fn ($q) => $q->latest()->limit(10),
            'alertRules' => fn ($q) => $q->where('is_active', true),
        ]);

        return Inertia::render('Vehicle/Show', [
            'vehicle' => $vehicle,
        ]);
    }

    public function edit(Vehicle $vehicle): Response
    {
        Gate::authorize('update', $vehicle->garage);

        $garages = Garage::where('user_id', auth()->id())->get(['id', 'name']);

        return Inertia::render('Vehicle/Edit', [
            'vehicle' => $vehicle->load('specs'),
            'garages' => $garages,
        ]);
    }

    public function update(Request $request, Vehicle $vehicle): RedirectResponse
    {
        Gate::authorize('update', $vehicle->garage);

        $validated = $request->validate([
            'garage_id' => ['required', 'exists:garages,id'],
            'plate' => ['required', 'string', 'max:10'],
            'vin' => ['nullable', 'string', 'max:17', 'unique:vehicles,vin,'.$vehicle->id],
            'brand' => ['required', 'string', 'max:50'],
            'model' => ['required', 'string', 'max:80'],
            'year' => ['required', 'integer', 'min:1900', 'max:'.now()->year + 1],
            'fuel_type' => ['required', 'in:gasolina,diesel,hibrido,electrico,glp'],
            'color' => ['nullable', 'string', 'max:40'],
            'current_km' => ['required', 'integer', 'min:0'],
        ]);

        $vehicle->update($validated);

        return redirect()->route('vehicles.show', $vehicle)->with('success', 'Vehículo actualizado');
    }

    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        Gate::authorize('update', $vehicle->garage);

        $vehicle->update(['is_active' => false]);

        return redirect()->route('vehicles.index')->with('success', 'Vehículo desactivado');
    }

    public function generateReport(Vehicle $vehicle)
    {
        Gate::authorize('view', $vehicle->garage);

        $action = new GenerateVehicleReportAction;
        $path = $action->execute($vehicle);

        return response()->download(storage_path('app/public/'.$path));
    }
}
