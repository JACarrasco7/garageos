<?php

namespace App\Modules\Vehicle\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Vehicle\Services\VehicleDocumentOcrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class VehicleDocumentOcrController extends Controller
{
    public function __construct(
        protected VehicleDocumentOcrService $ocrService
    ) {}

    public function create()
    {
        return Inertia::render('Vehicle/ImportDocument');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'document' => ['required', 'file', 'image', 'max:10240'],
        ]);

        $path = Storage::put('temp/ocr', $validated['document']);
        $fullPath = Storage::path($path);

        $data = $this->ocrService->extractVehicleData($fullPath);

        Storage::delete($path);

        return back()->with([
            'extracted_data' => $data,
            'document_path' => $validated['document']->store('documents'),
        ]);
    }
}
