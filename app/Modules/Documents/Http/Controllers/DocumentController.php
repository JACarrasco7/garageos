<?php

namespace App\Modules\Documents\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Documents\Models\Document;
use App\Modules\Vehicle\Models\Vehicle;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DocumentController extends Controller
{
    public function index(Vehicle $vehicle): Response
    {
        $this->authorize('view', $vehicle->garage);

        $documents = $vehicle->documents()
            ->latest()
            ->get()
            ->groupBy('type');

        return Inertia::render('Documents/Index', [
            'vehicle' => $vehicle,
            'documents' => $documents,
        ]);
    }

    public function create(Vehicle $vehicle): Response
    {
        $this->authorize('update', $vehicle->garage);
        return Inertia::render('Documents/Upload', ['vehicle' => $vehicle]);
    }

    public function store(Request $request, Vehicle $vehicle): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('update', $vehicle->garage);

        $validated = $request->validate([
            'type' => ['required', 'in:factura,itv,seguro,impuesto,otro'],
            'title' => ['nullable', 'string', 'max:150'],
            'file' => ['required', 'file', 'max:20480'],
            'document_date' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date'],
            'amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $path = $request->file('file')->store('documents', 'public');

        $document = $vehicle->documents()->create([
            'type' => $validated['type'],
            'title' => $validated['title'] ?? $request->file('file')->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $request->file('file')->getSize(),
            'mime_type' => $request->file('file')->getMimeType(),
            'document_date' => $validated['document_date'],
            'expiry_date' => $validated['expiry_date'],
            'amount' => $validated['amount'],
            'km_at_time' => $vehicle->current_km,
        ]);

        // Disparar job de OCR
        \App\Modules\Documents\Jobs\ParseDocumentJob::dispatch($document);

        if (in_array($validated['type'], ['itv', 'seguro']) && $validated['expiry_date']) {
            $vehicle->alertRules()->create([
                'type' => $validated['type'],
                'trigger_date' => $validated['expiry_date'],
                'advance_days' => 30,
            ]);
        }

        return back()->with('success', 'Documento subido correctamente');
    }

    public function show(Document $document): Response
    {
        $this->authorize('view', $document->vehicle->garage);

        return Inertia::render('Documents/Show', [
            'document' => $document,
        ]);
    }
}
