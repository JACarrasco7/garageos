<?php

namespace App\Modules\Documents\Jobs;

use App\Modules\Documents\Events\DocumentProcessed;
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

        if (! file_exists($path)) {
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
        event(new DocumentProcessed($this->document));
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
        $parser = new Parser;
        $pdf = $parser->parseFile($path);

        return $pdf->getText();
    }

    private function extractFromImage(string $path): string
    {
        $outputFile = sys_get_temp_dir().'/ocr_'.uniqid();
        $command = sprintf('tesseract %s %s -l spa+eng --psm 1 2>&1', escapeshellarg($path), escapeshellarg($outputFile));
        exec($command, $output, $returnCode);

        if ($returnCode === 0 && file_exists($outputFile.'.txt')) {
            $text = file_get_contents($outputFile.'.txt');
            unlink($outputFile.'.txt');

            return $text;
        }

        return '';
    }

    private function parseData(string $text): array
    {
        return [
            'dates_found' => $this->findDates($text),
            'document_type' => $this->detectType($text),
            'amount' => $this->extractAmount($text),
            'km' => $this->extractKm($text),
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

        if (str_contains($text, 'factura') || str_contains($text, 'invoice')) {
            return 'factura';
        }

        return null;
    }

    private function extractAmount(string $text): ?float
    {
        // Buscar cantidades como "123,45 €" o "123.45 EUR"
        preg_match('/(\d{1,3}(?:\.\d{3})*,\d{2})\s*(?:€|EUR)/i', $text, $matches);
        if (isset($matches[1])) {
            return floatval(str_replace(',', '.', $matches[1]));
        }

        preg_match('/(\d{1,3}(?:\.\d{3})*)\.\d{2}\s*(?:€|EUR)/i', $text, $matches);
        if (isset($matches[1])) {
            return floatval($matches[1]);
        }

        return null;
    }

    private function extractKm(string $text): ?int
    {
        // Buscar "km: 12345" o "12345 km"
        preg_match('/(?:km|kilómetros?)[:\s]*(\d{1,6})/i', $text, $matches);
        if (isset($matches[1])) {
            return (int) $matches[1];
        }

        return null;
    }
}
