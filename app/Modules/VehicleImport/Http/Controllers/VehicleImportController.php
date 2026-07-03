<?php

namespace App\Modules\VehicleImport\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\VehicleImport\Actions\AdvanceImportStepAction;
use App\Modules\VehicleImport\Actions\ImportCertificateAction;
use App\Modules\VehicleImport\Actions\ImportValuationAction;
use App\Modules\VehicleImport\Actions\UploadImportDocumentAction;
use App\Modules\VehicleImport\Enums\ImportStep;
use App\Modules\VehicleImport\Models\ImportDocument;
use App\Modules\VehicleImport\Models\VehicleImport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class VehicleImportController extends Controller
{
    public function index(): Response
    {
        $imports = VehicleImport::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return Inertia::render('Import/Index', [
            'imports' => $imports,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Import/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'plate_original' => ['nullable', 'string', 'max:20'],
            'brand' => ['required', 'string', 'max:50'],
            'model' => ['required', 'string', 'max:80'],
            'year' => ['required', 'integer', 'min:1900', 'max:'.(now()->year + 1)],
            'engine_cc' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'power_kw' => ['nullable', 'integer', 'min:0', 'max:2000'],
            'co2_emissions' => ['nullable', 'integer', 'min:0', 'max:500'],
            'origin_country' => ['nullable', 'string', 'size:2'],
        ]);

        $import = VehicleImport::create([
            ...$validated,
            'user_id' => auth()->id(),
            'origin_country' => $validated['origin_country'] ?? 'DE',
            'current_step' => ImportStep::PURCHASE,
        ]);

        return redirect()->route('import.wizard', $import->id);
    }

    public function wizard(VehicleImport $import): Response
    {
        $this->authorize('view', $import);

        return Inertia::render('Import/Wizard', [
            'import' => VehicleImportResource::make($import),
            'steps' => collect(ImportStep::cases())
                ->filter(fn ($step) => $step !== ImportStep::COMPLETED)
                ->map(fn ($step) => [
                    'id' => $step->value,
                    'label' => $step->getLabel(),
                    'order' => $step->getOrder(),
                    'required_docs' => $step->getRequiredDocuments(),
                ])
                ->values(),
        ]);
    }

    public function updateStep(Request $request, VehicleImport $import): JsonResponse
    {
        $this->authorize('update', $import);

        $validated = $request->validate([
            'step' => ['required', 'string', 'in:'.collect(ImportStep::cases())->pluck('value')->join(',')],
        ]);

        $targetStep = ImportStep::from($validated['step']);

        $action = new AdvanceImportStepAction;
        $import = $action->execute($import, $targetStep);

        return response()->json([
            'import' => VehicleImportResource::make($import),
        ]);
    }

    public function uploadDocument(Request $request, VehicleImport $import): JsonResponse
    {
        $this->authorize('update', $import);

        $validated = $request->validate([
            'step' => ['required', 'string'],
            'type' => ['required', 'string'],
            'file' => ['required', 'file', 'max:10240'],
        ]);

        $step = ImportStep::from($validated['step']);

        $action = new UploadImportDocumentAction;
        $document = $action->execute(
            $import,
            $step,
            $validated['type'],
            $request->file('file')
        );

        return response()->json([
            'document' => [
                'id' => $document->id,
                'type' => $document->type,
                'type_label' => $document->getTypeLabel(),
                'file_path' => $document->file_path,
                'file_url' => Storage::url($document->file_path),
                'is_verified' => $document->is_verified,
            ],
        ]);
    }

    public function verifyDocument(ImportDocument $document): JsonResponse
    {
        $this->authorize('update', $document->vehicleImport);

        $action = new UploadImportDocumentAction;
        $document = $action->verifyDocument($document);

        return response()->json([
            'document' => [
                'id' => $document->id,
                'is_verified' => $document->is_verified,
            ],
            'import' => VehicleImportResource::make($document->vehicleImport),
        ]);
    }

    public function updatePurchase(Request $request, VehicleImport $import): JsonResponse
    {
        $this->authorize('update', $import);

        $validated = $request->validate([
            'plate_original' => ['nullable', 'string', 'max:20'],
            'purchase_date' => ['nullable', 'date'],
        ]);

        $import->update($validated);

        return response()->json([
            'import' => VehicleImportResource::make($import),
        ]);
    }

    public function updateTransport(Request $request, VehicleImport $import): JsonResponse
    {
        $this->authorize('update', $import);

        $validated = $request->validate([
            'arrival_date' => ['nullable', 'date'],
        ]);

        if (isset($validated['arrival_date'])) {
            $import->arrival_date = $validated['arrival_date'];
            $import->calculateItvDeadline();
            $import->save();
        }

        return response()->json([
            'import' => VehicleImportResource::make($import),
        ]);
    }

    public function addTemporaryPlate(Request $request, VehicleImport $import): JsonResponse
    {
        $this->authorize('update', $import);

        $validated = $request->validate([
            'plate_number' => ['required', 'string', 'max:20'],
            'issued_at' => ['required', 'date'],
            'expires_at' => ['required', 'date', 'after:issued_at'],
        ]);

        $plate = $import->temporaryPlates()->create($validated);

        return response()->json([
            'plate' => [
                'id' => $plate->id,
                'plate_number' => $plate->plate_number,
                'issued_at' => $plate->issued_at->toDateString(),
                'expires_at' => $plate->expires_at->toDateString(),
                'is_expired' => $plate->isExpired(),
                'days_until_expiry' => $plate->getDaysUntilExpiry(),
            ],
        ]);
    }

    public function completeImport(VehicleImport $import): JsonResponse
    {
        $this->authorize('update', $import);

        $action = new AdvanceImportStepAction;
        $import = $action->completeImport($import);

        return response()->json([
            'import' => VehicleImportResource::make($import),
        ]);
    }

    public function destroy(VehicleImport $import): RedirectResponse
    {
        $this->authorize('delete', $import);

        foreach ($import->importDocuments as $doc) {
            if (Storage::disk('public')->exists($doc->file_path)) {
                Storage::disk('public')->delete($doc->file_path);
            }
        }

        $import->delete();

        return redirect()->route('imports.index');
    }

    public function valuation(VehicleImport $import): JsonResponse
    {
        $this->authorize('view', $import);

        $action = new ImportValuationAction;
        $result = $action->execute($import);

        return response()->json([
            'valuation' => $result['valuation'],
            'taxes' => $result['taxes'],
            'total_cost' => $result['total_cost'],
        ]);
    }

    public function generateCertificate(VehicleImport $import): JsonResponse
    {
        $this->authorize('update', $import);

        $action = new ImportCertificateAction;
        $path = $action->execute($import);

        return response()->json([
            'certificate_path' => $path,
            'certificate_url' => Storage::url($path),
        ]);
    }
}
