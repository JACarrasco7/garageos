<?php

namespace App\Modules\VehicleImport\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\VehicleImport\Models\ImportDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ImportDocumentController extends Controller
{
    public function destroy(ImportDocument $document): JsonResponse
    {
        $this->authorize('update', $document->vehicleImport);

        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return response()->json(['success' => true]);
    }
}
