<?php

namespace App\Modules\VehicleImport\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\VehicleImport\Models\ImportDocument;
use App\Modules\VehicleImport\Services\DocumentVerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentVerificationController extends Controller
{
    public function __construct(
        protected DocumentVerificationService $verificationService
    ) {}

    /**
     * Verificar DNI/NIE.
     */
    public function validateId(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'document' => ['required', 'string', 'max:20'],
            'type' => ['required', 'in:spain,dni,nie'],
        ]);

        $result = $this->verificationService->validateSpanishId($validated['document']);

        return response()->json($result);
    }

    /**
     * Verificar documento subido.
     */
    public function verifyDocument(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'type' => ['required', 'in:coc,invoice,contract,itv'],
        ]);

        $path = $request->file('document')->store('verification', 'public');

        $result = $this->verificationService->verifyImportDocument($path, $validated['type']);

        if (!$result['success']) {
            Storage::disk('public')->delete($path);
        }

        return response()->json($result);
    }

    /**
     * Verificar contrato completo.
     */
    public function verifyContract(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'seller_full_name' => ['required', 'string', 'max:100'],
            'seller_document_id' => ['required', 'string', 'max:20'],
            'seller_address' => ['required', 'string', 'max:200'],
            'buyer_full_name' => ['required', 'string', 'max:100'],
            'buyer_document_id' => ['required', 'string', 'max:20'],
            'buyer_address' => ['required', 'string', 'max:200'],
            'agreed_price' => ['required', 'numeric', 'min:0'],
            'contract_date' => ['required', 'date'],
        ]);

        $result = $this->verificationService->verifyContract($validated);

        return response()->json($result);
    }
}
