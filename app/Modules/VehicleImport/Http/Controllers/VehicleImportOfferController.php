<?php

namespace App\Modules\VehicleImport\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\VehicleImport\Actions\AcceptImportOfferAction;
use App\Modules\VehicleImport\Http\Requests\StoreVehicleImportOfferRequest;
use App\Modules\VehicleImport\Models\VehicleImportOffer;
use App\Modules\VehicleImport\Models\VehicleImportRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class VehicleImportOfferController extends Controller
{
    public function index(): Response
    {
        $requests = VehicleImportRequest::open()
            ->with(['offers.provider'])
            ->latest()
            ->paginate(10);

        return Inertia::render('VehicleImport/Offers/Index', [
            'requests' => $requests,
        ]);
    }

    public function store(StoreVehicleImportOfferRequest $request, AcceptImportOfferAction $action): RedirectResponse
    {
        $offer = VehicleImportOffer::create([
            ...$request->validated(),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('vehicle-import.offers.index')
            ->with('success', 'Oferta enviada correctamente');
    }

    public function accept(VehicleImportOffer $offer, AcceptImportOfferAction $action): RedirectResponse
    {
        $action->execute($offer);

        return back()->with('success', 'Oferta aceptada. Se ha iniciado el proceso de importación.');
    }
}
