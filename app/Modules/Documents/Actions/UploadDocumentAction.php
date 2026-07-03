<?php

namespace App\Modules\Documents\Actions;

use App\Modules\Documents\Jobs\ParseDocumentJob;
use App\Modules\Documents\Models\Document;
use App\Modules\Vehicle\Models\Vehicle;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class UploadDocumentAction
{
    /**
     * Upload a document and associate it with a vehicle.
     *
     * @param  UploadedFile  $file
     *
     * @throws \Exception
     */
    public function execute(Vehicle $vehicle, array $data, $file): Document
    {
        return DB::transaction(function () use ($vehicle, $data, $file) {
            // 1. Store the file using Spatie MediaLibrary
            $media = $vehicle->addMedia($file)
                ->toMediaCollection('documents');

            // 2. Create the document record
            $document = Document::create([
                'vehicle_id' => $vehicle->id,
                'type' => $data['type'],
                'title' => $data['title'] ?? $file->getClientOriginalName(),
                'file_path' => $media->getPath(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'km_at_time' => $data['km_at_time'] ?? $vehicle->current_km,
                'document_date' => $data['document_date'] ?? null,
                'expiry_date' => $data['expiry_date'] ?? null,
                'amount' => $data['amount'] ?? null,
                'parsed_data' => $data['parsed_data'] ?? [],
            ]);

            // 3. Dispatch OCR job
            ParseDocumentJob::dispatch($document);

            return $document;
        });
    }
}
