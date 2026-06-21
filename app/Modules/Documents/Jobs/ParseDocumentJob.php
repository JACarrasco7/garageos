<?php

namespace App\Modules\Documents\Jobs;

use App\Modules\Documents\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ParseDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Document $document) {}

    public function handle(): void
    {
        $path = Storage::path($this->document->file_path);

        if (!file_exists($path)) {
            return;
        }

        // OCR básico con Tesseract (si está disponible)
        $text = $this->extractText($path);

        // Extraer fechas y tipo de documento
        $this->document->update([
            'extracted_text' => $text,
            'parsed_data' => $this->parseData($text),
        ]);
    }

    private function extractText(string $path): string
    {
        $ext = pathinfo($path, PATHINFO_EXTENSION);

        if ($ext === 'pdf') {
            return $this->extractFromPdf($path);
        }

        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            return $this->extractFromImage($path);
        }

        return '';
    }

    private function extractFromPdf(string $path): string
    {
        // Placeholder: usar pdftotext o smalot/pdfparser
        return '';
    }

    private function extractFromImage(string $path): string
    {
        // Placeholder: usar tesseract OCR
        // exec("tesseract {$path} stdout", $output);
        return '';
    }

    private function parseData(string $text): array
    {
        return [
            'dates_found' => $this->findDates($text),
            'document_type' => $this->detectType($text),
        ];
    }

    private function findDates(string $text): array
    {
        preg_match_all('/\b\d{2}[\/\-]\d{2}[\/\-]\d{4}\b/', $text, $matches);
        return $matches[0] ?? [];
    }

    private function detectType(string $text): ?string
    {
        $text = strtolower($text);

        if (str_contains($text, 'itv') || str_contains($text, 'inspección técnica')) {
            return 'itv';
        }

        if (str_contains($text, 'seguro') || str_contains($text, 'insurance')) {
            return 'seguro';
        }

        if (str_contains($text, 'ficha') || str_contains($text, 'técnica')) {
            return 'ficha';
        }

        return null;
    }
}