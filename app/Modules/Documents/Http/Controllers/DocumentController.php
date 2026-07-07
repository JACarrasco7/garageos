<?php

namespace App\Modules\Documents\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Documents\Actions\UploadDocumentAction;
use App\Modules\Documents\Events\DocumentUploaded;
use App\Modules\Documents\Http\Requests\StoreDocumentRequest;
use App\Modules\Documents\Models\Document;
use App\Modules\Vehicle\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DocumentController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $vehicleIds = Vehicle::query()
            ->whereHas('garage', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->pluck('id');

        $vehicles = Vehicle::whereIn('id', $vehicleIds)->get();
        $documents = Document::whereIn('vehicle_id', $vehicleIds)
            ->latest()
            ->get()
            ->groupBy('type');

        return Inertia::render('Documents/Index', [
            'vehicle' => $vehicles->first(),
            'vehicles' => $vehicles,
            'documents' => $documents,
        ]);
    }

    public function indexForVehicle(Vehicle $vehicle): Response
    {
        $this->authorize('view', $vehicle->garage);

        $documents = $vehicle->documents()
            ->latest()
            ->get()
            ->groupBy('type');

        return Inertia::render('Documents/Index', [
            'vehicle' => $vehicle,
            'vehicles' => collect([$vehicle]),
            'documents' => $documents,
        ]);
    }

    public function create(Vehicle $vehicle): Response
    {
        $this->authorize('update', $vehicle->garage);

        return Inertia::render('Documents/Upload', ['vehicle' => $vehicle]);
    }

    public function store(StoreDocumentRequest $request, UploadDocumentAction $action): RedirectResponse
    {
        $this->authorize('update', $request->vehicle->garage);

        $document = $action->execute(
            $request->vehicle,
            $request->validated(),
            $request->file('file')
        );

        return redirect()->route('vehicles.show', $request->vehicle->id)
            ->with('success', 'Documento subido correctamente.');
    }

    public function show(Document $document): Response
    {
        $this->authorize('view', $document->vehicle->garage);

        return Inertia::render('Documents/Show', [
            'document' => $document,
        ]);
    }

    public function mobileIndex(Vehicle $vehicle): Response
    {
        $this->authorize('view', $vehicle->garage);

        $documents = $vehicle->documents()
            ->latest()
            ->get();

        return Inertia::render('Mobile/Documents/Index', [
            'vehicle' => $vehicle,
            'documents' => $documents,
        ]);
    }

    public function mobileCreate(Vehicle $vehicle): Response
    {
        $this->authorize('update', $vehicle->garage);

        return Inertia::render('Mobile/Documents/Upload', ['vehicle' => $vehicle]);
    }

    public function mobileStore(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $this->authorize('update', $vehicle->garage);

        $validated = $request->validate([
            'type' => ['required', 'in:factura,itv,seguro,impuesto,otro'],
            'title' => ['nullable', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:10240'],
            'document_date' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date'],
            'amount' => ['nullable', 'numeric'],
        ]);

        $file = $request->file('file');
        $path = $file->store('documents/'.$vehicle->id, 'private');

        $document = Document::create([
            'vehicle_id' => $vehicle->id,
            'type' => $validated['type'],
            'title' => $validated['title'] ?? $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'document_date' => $validated['document_date'],
            'expiry_date' => $validated['expiry_date'],
            'amount' => $validated['amount'],
            'km_at_time' => $vehicle->current_km,
        ]);

        event(new DocumentUploaded($document));

        return redirect()->route('mobile.vehicles.show', $vehicle->id)
            ->with('success', 'Documento subido correctamente.');
    }
}
