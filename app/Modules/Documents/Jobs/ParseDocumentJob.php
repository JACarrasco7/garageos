<?php

namespace App\Modules\Documents\Jobs;

use App\Modules\Documents\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

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

        // Disparar evento de documento procesado
        event(new \App\Modules\Documents\Events\DocumentProcessed($this->document));
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
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($path);
        return $pdf->getText();
    }

    private function extractFromImage(string $path): string
    {
        $outputFile = sys_get_temp_dir() . '/ocr_' . uniqid();
        $command = sprintf('tesseract %s %s -l spa+eng --psm 1 2>&1', escapeshellarg($path), escapeshellarg($outputFile));
        exec($command, $output, $returnCode);
        
        if ($returnCode === 0 && file_exists($outputFile . '.txt')) {
            $text = file_get_contents($outputFile . '.txt');
            unlink($outputFile . '.txt');
            return $text;
        }
        
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
