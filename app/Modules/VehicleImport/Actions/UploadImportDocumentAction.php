<?php

namespace App\Modules\VehicleImport\Actions;

use App\Modules\VehicleImport\Models\VehicleImport;
use App\Modules\VehicleImport\Models\ImportDocument;
use App\Modules\VehicleImport\Enums\ImportStep;
use Illuminate\Support\Facades\Storage;

class UploadImportDocumentAction
{
    public function execute(
        VehicleImport $import,
        ImportStep $step,
        string $documentType,
        UploadedFile $file
    ): ImportDocument {
        $path = $this->storeFile($file, $import->id);

        $document = $import->importDocuments()->create([
            'step' => $step,
            'type' => $documentType,
            'file_path' => $path,
            'is_verified' => false,
        ]);

        return $document;
    }

    protected function storeFile(UploadedFile $file, int $importId): string
    {
        $filename = time() . '_' . $file->getClientOriginalName();
        return $file->storeAs("import-documents/{$importId}", $filename, 'public');
    }

    public function verifyDocument(ImportDocument $document): ImportDocument
    {
        $document->update([
            'is_verified' => true,
        ]);

        $this->checkAutoAdvance($document->vehicleImport);

        return $document->fresh();
    }

    protected function checkAutoAdvance(VehicleImport $import): void
    {
        $action = new AdvanceImportStepAction();
        $action->autoAdvanceIfReady($import);
    }

    public function deleteDocument(ImportDocument $document): void
    {
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();
    }
}