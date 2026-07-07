<?php

namespace App\Modules\Vehicle\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VehicleDocumentOcrService
{
    protected string $apiKey;
    protected string $apiUrl = 'https://api.ocr.space/parse/image';

    public function __construct()
    {
        $this->apiKey = config('services.ocr.api_key');
    }

    public function extractVehicleData(string $imagePath): array
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

        return $this->parseVehicleText($text);
    }

    protected function extractLocal(string $imagePath): array
    {
        $text = $this->extractTextFromImage($imagePath);
        return $this->parseVehicleText($text);
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

    protected function parseVehicleText(string|array $lines): array
    {
        $data = [
            'brand' => null,
            'model' => null,
            'year' => null,
            'mileage_km' => null,
            'fuel_type' => null,
            'power_hp' => null,
            'gearbox' => null,
            'co2_emissions' => null,
        ];

        $text = is_array($lines) ? implode("\n", $lines) : $lines;
        $textLower = strtolower($text);

        $data['brand'] = $this->extractBrand($text);
        $data['model'] = $this->extractModel($text);
        $data['year'] = $this->extractYear($text);
        $data['mileage_km'] = $this->extractMileage($text);
        $data['fuel_type'] = $this->extractFuelType($textLower);
        $data['power_hp'] = $this->extractPower($text);
        $data['gearbox'] = $this->extractGearbox($textLower);
        $data['co2_emissions'] = $this->extractCo2($text);

        return $data;
    }

    protected function extractBrand(string $text): ?string
    {
        $brands = ['audi', 'bmw', 'mercedes', 'volkswagen', 'vw', 'seat', 'skoda', 'ford', 'peugeot', 'citroen', 'opel', 'renault'];
        $textLower = strtolower($text);

        foreach ($brands as $brand) {
            if (str_contains($textLower, $brand)) {
                return ucfirst($brand);
            }
        }

        return null;
    }

    protected function extractModel(string $text): ?string
    {
        if (preg_match('/([A-Z][a-z]+\s*\d+[A-Z]?)/i', $text, $matches)) {
            return $matches[1];
        }
        return null;
    }

    protected function extractYear(string $text): ?int
    {
        if (preg_match('/\b(19|20)\d{2}\b/', $text, $matches)) {
            return (int) $matches[0];
        }
        return null;
    }

    protected function extractMileage(string $text): ?int
    {
        if (preg_match('/(\d{1,3}(?:\.\d{3})*\s*(?:km|kilómetros))/i', $text, $matches)) {
            return (int) str_replace('.', '', str_replace('km', '', $matches[0]));
        }
        return null;
    }

    protected function extractFuelType(string $text): ?string
    {
        $fuelTypes = ['diesel', 'gasolina', 'hibrido', 'electrico', 'glp', 'gnv'];
        foreach ($fuelTypes as $fuel) {
            if (str_contains($text, $fuel)) {
                return $fuel;
            }
        }
        return null;
    }

    protected function extractPower(string $text): ?int
    {
        if (preg_match('/(\d+)\s*(?:CV|HP|kW)/i', $text, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }

    protected function extractGearbox(string $text): ?string
    {
        if (str_contains($text, 'manual')) return 'manual';
        if (str_contains($text, 'automático') || str_contains($text, 'automatic')) return 'automatico';
        return null;
    }

    protected function extractCo2(string $text): ?int
    {
        if (preg_match('/(\d+)\s*g\/km.*CO2/i', $text, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }

    public function extractInvoiceData(string $imagePath): array
    {
        $text = $this->extractTextFromImage($imagePath);

        return [
            'total' => $this->extractInvoiceTotal($text),
            'date' => $this->extractInvoiceDate($text),
            'vat_number' => $this->extractVatNumber($text),
            'seller_name' => $this->extractSellerName($text),
        ];
    }

    protected function extractInvoiceTotal(string $text): ?float
    {
        if (preg_match('/total\s*[:\-]?\s*(\d+[,\.]\d{2})/i', $text, $matches)) {
            return (float) str_replace(',', '.', $matches[1]);
        }
        return null;
    }

    protected function extractInvoiceDate(string $text): ?string
    {
        if (preg_match('/fecha\s*[:\-]?\s*(\d{4}-\d{2}-\d{2})/i', $text, $matches)) {
            return $matches[1];
        }
        if (preg_match('/(\d{2}\/\d{2}\/\d{4})/i', $text, $matches)) {
            $parts = explode('/', $matches[1]);
            return "{$parts[2]}-{$parts[1]}-{$parts[0]}";
        }
        return null;
    }

    protected function extractVatNumber(string $text): ?string
    {
        if (preg_match('/N[º|º]\s*(\d{2}[A-Z]\d{7}[A-Z])/i', $text, $matches)) {
            return $matches[1];
        }
        if (preg_match('/VAT\s*[:\-]?\s*([A-Z]{2}\d{9,10})/i', $text, $matches)) {
            return $matches[1];
        }
        return null;
    }

    protected function extractSellerName(string $text): ?string
    {
        if (preg_match('/vendedor\s*[:\-]?\s*(.+)/i', $text, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }
}
