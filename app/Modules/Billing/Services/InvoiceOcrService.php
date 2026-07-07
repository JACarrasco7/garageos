<?php

namespace App\Modules\Billing\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class InvoiceOcrService
{
    protected string $apiKey;
    protected string $apiUrl = 'https://api.ocr.space/parse/image';

    public function __construct()
    {
        $this->apiKey = config('services.ocr.api_key');
    }

    public function extractInvoiceData(string $imagePath): array
    {
        if (!$this->apiKey) {
            return $this->extractLocal($imagePath);
        }

        $response = Http::asForm()->post($this->apiUrl, [
            'apikey' => $this->apiKey,
            'language' => 'spa',
            'isOverlayRequired' => 'true',
            'file' => fopen($imagePath, 'r'),
        ]);

        if (!$response->successful()) {
            return $this->extractLocal($imagePath);
        }

        $data = $response->json();
        $text = $data['ParsedResults'][0]['TextOverlay']['Lines'] ?? [];

        return $this->parseInvoiceText($text);
    }

    protected function extractLocal(string $imagePath): array
    {
        $text = $this->extractTextFromImage($imagePath);
        return $this->parseInvoiceText($text);
    }

    protected function extractTextFromImage(string $imagePath): string
    {
        $image = imagecreatefromjpeg($imagePath);
        $text = '';

        if ($image) {
            $text = $this->tesseractOcr($image);
            imagedestroy($image);
        }

        return $text;
    }

    protected function tesseractOcr($image): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'ocr_');
        imagejpeg($image, $tempFile);

        $output = shell_exec("tesseract {$tempFile} stdout --oem 1 -l spa 2>/dev/null");
        unlink($tempFile);

        return $output ?: '';
    }

    protected function parseInvoiceText(string|array $lines): array
    {
        $data = [
            'invoice_number' => null,
            'invoice_date' => null,
            'due_date' => null,
            'total_amount' => null,
            'tax_amount' => null,
            'issuer_name' => null,
            'issuer_nif' => null,
        ];

        $text = is_array($lines) ? implode("\n", $lines) : $lines;
        $textLower = strtolower($text);

        $data['invoice_number'] = $this->extractInvoiceNumber($text);
        $data['invoice_date'] = $this->extractDate($text);
        $data['due_date'] = $this->extractDueDate($text);
        $data['total_amount'] = $this->extractTotalAmount($text);
        $data['tax_amount'] = $this->extractTaxAmount($text);
        $data['issuer_name'] = $this->extractIssuerName($text);
        $data['issuer_nif'] = $this->extractIssuerNif($text);

        return $data;
    }

    protected function extractInvoiceNumber(string $text): ?string
    {
        if (preg_match('/(?:Factura|Factura Nº|FACTURA)\s*[\d\-A-Z]+/i', $text, $matches)) {
            preg_match('/[\d\-A-Z]+/', $matches[0], $num);
            return $num[0] ?? null;
        }
        return null;
    }

    protected function extractDate(string $text): ?string
    {
        if (preg_match('/(\d{2}\/\d{2}\/\d{4}|\d{2}\-\d{2}\-\d{4})/', $text, $matches)) {
            return $matches[0];
        }
        return null;
    }

    protected function extractDueDate(string $text): ?string
    {
        if (preg_match('/(?:Fecha.*vencimiento|Vencimiento|Due).*?(\d{2}\/\d{2}\/\d{4}|\d{2}\-\d{2}\-\d{4})?/i', $text, $matches)) {
            return $matches[1] ?? $this->extractDate($text);
        }
        return $this->extractDate($text);
    }

    protected function extractTotalAmount(string $text): ?float
    {
        if (preg_match('/(?:Importe|Total|TOTAL).*?(\d{1,3}(?:\.\d{3})*,\d{2})/', $text, $matches)) {
            return (float) str_replace(',', '.', $matches[1]);
        }
        if (preg_match('/(\d{1,3}(?:\.\d{3})*,\d{2})\s*€/', $text, $matches)) {
            return (float) str_replace(',', '.', $matches[1]);
        }
        return null;
    }

    protected function extractTaxAmount(string $text): ?float
    {
        if (preg_match('/IVA.*?(\d{1,3}(?:\.\d{3})*,\d{2})/', $text, $matches)) {
            return (float) str_replace(',', '.', $matches[1]);
        }
        return null;
    }

    protected function extractIssuerName(string $text): ?string
    {
        if (preg_match('/Emisor:\s*(.+)/i', $text, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    protected function extractIssuerNif(string $text): ?string
    {
        if (preg_match('/NIF:\s*([A-Z\d\-]+)/i', $text, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
