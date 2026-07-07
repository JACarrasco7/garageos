<?php

namespace App\Modules\VehicleImport\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\VehicleImport\Http\Requests\StoreVehicleImportRequestRequest;
use App\Modules\VehicleImport\Models\VehicleImportRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class VehicleImportRequestController extends Controller
{
    public function index(): Response
    {
        $requests = VehicleImportRequest::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return Inertia::render('VehicleImport/Requests/Index', [
            'requests' => $requests,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('VehicleImport/Requests/Create');
    }

    public function store(StoreVehicleImportRequestRequest $request): RedirectResponse
    {
        VehicleImportRequest::create([
            'user_id' => auth()->id(),
            ...$request->validated(),
        ]);

        return redirect()->route('vehicle-import.requests.index')
            ->with('success', 'Solicitud creada correctamente');
    }

    public function show(VehicleImportRequest $request): Response
    {
        $this->authorize('view', $request);

        $request->load(['offers.provider']);
        $request->setRelation('offers', $request->offers->each->append(['rating_avg', 'rating_count']));

        return Inertia::render('VehicleImport/Requests/Show', [
            'request' => $request,
        ]);
    }
}
