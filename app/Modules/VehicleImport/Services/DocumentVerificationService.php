<?php

namespace App\Modules\VehicleImport\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DocumentVerificationService
{
    protected string $googleVisionApiKey;
    protected string $ocrEngine;

    public function __construct()
    {
        $this->googleVisionApiKey = config('services.google_vision.key');
        $this->ocrEngine = config('services.ocr_engine', 'tesseract');
    }

    /**
     * Validar DNI/NIE español.
     */
    public function validateSpanishId(string $dni): array
    {
        $dni = strtoupper(str_replace(' ', '', $dni));

        if (!preg_match('/^[XYZ]\d{7}[A-Z]$/', $dni)) {
            return ['valid' => false, 'error' => 'Formato inválido. Debe ser DNI (8 dígitos + letra) o NIE (X/Y/Z + 7 dígitos + letra)'];
        }

        // Reemplazar X por 0, Y por 1, Z por 2
        $letterMap = ['X' => 0, 'Y' => 1, 'Z' => 2];
        $dniNumber = strtr(substr($dni, 0, 8), $letterMap);
        $letter = substr($dni, 8, 1);

        $letters = 'TRWAGMYFPDXBNJZSQVHLCKE';
        $expectedLetter = $letters[intval($dniNumber) % 23];

        if ($letter !== $expectedLetter) {
            return ['valid' => false, 'error' => 'Letra de control incorrecta'];
        }

        return ['valid' => true, 'type' => $dniNumber[0] === '0' ? 'DNI' : 'NIE'];
    }

    /**
     * Extraer texto de documento usando OCR.
     */
    public function extractTextFromDocument(string $filePath): array
    {
        if ($this->ocrEngine === 'google_vision' && $this->googleVisionApiKey) {
            return $this->extractWithGoogleVision($filePath);
        }

        return $this->extractWithTesseract($filePath);
    }

    /**
     * Extraer texto con Google Vision API.
     */
    protected function extractWithGoogleVision(string $filePath): array
    {
        $content = Storage::get($filePath);
        $base64 = base64_encode($content);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://vision.googleapis.com/v1/images:annotate?key={$this->googleVisionApiKey}", [
            'requests' => [
                [
                    'image' => ['content' => $base64],
                    'features' => [
                        ['type' => 'TEXT_DETECTION', 'maxResults' => 1],
                    ],
                ],
            ],
        ]);

        if (!$response->successful()) {
            return ['success' => false, 'error' => 'Error en la API de Google Vision'];
        }

        $text = $response->json('requests.0.fullTextAnnotation.text');

        return [
            'success' => true,
            'text' => $text,
            'confidence' => $response->json('requests.0.fullTextAnnotation.textAnnotations.0.confidence', 0),
        ];
    }

    /**
     * Extraer texto con Tesseract (requiere extensión PHP).
     */
    protected function extractWithTesseract(string $filePath): array
    {
        $fullPath = storage_path("app/public/{$filePath}");

        if (!file_exists($fullPath)) {
            return ['success' => false, 'error' => 'Archivo no encontrado'];
        }

        // Usar Tesseract vía exec (requiere instalar tesseract)
        $outputPath = sys_get_temp_dir() . '/ocr_' . uniqid();
        $command = "tesseract \"{$fullPath}\" \"{$outputPath}\" -l spa 2>/dev/null";

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            return ['success' => false, 'error' => 'Error al procesar el documento'];
        }

        $text = file_get_contents($outputPath . '.txt');

        return [
            'success' => true,
            'text' => $text,
            'confidence' => 0.85, // Tesseract no devuelve confianza directamente
        ];
    }

    /**
     * Verificar contrato de importación.
     */
    public function verifyContract(array $contractData): array
    {
        $errors = [];

        // Validar DNI comprador
        if (!empty($contractData['buyer_document_id'])) {
            $dniResult = $this->validateSpanishId($contractData['buyer_document_id']);
            if (!$dniResult['valid']) {
                $errors[] = "DNI comprador inválido: {$dniResult['error']}";
            }
        }

        // Validar DNI vendedor
        if (!empty($contractData['seller_document_id'])) {
            $dniResult = $this->validateSpanishId($contractData['seller_document_id']);
            if (!$dniResult['valid']) {
                $errors[] = "DNI vendedor inválido: {$dniResult['error']}";
            }
        }

        // Validar campos obligatorios
        $required = ['seller_full_name', 'buyer_full_name', 'agreed_price', 'contract_date'];
        foreach ($required as $field) {
            if (empty($contractData[$field])) {
                $errors[] = "Campo obligatorio faltante: {$field}";
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'verified_at' => now()->toISOString(),
        ];
    }

    /**
     * Verificar documento de importación (CoC, factura, etc.).
     */
    public function verifyImportDocument(string $filePath, string $documentType): array
    {
        $result = $this->extractTextFromDocument($filePath);

        if (!$result['success']) {
            return $result;
        }

        $text = $result['text'];
        $verification = [
            'type' => $documentType,
            'extracted_text' => $text,
            'confidence' => $result['confidence'],
            'checks' => [],
        ];

        // Verificaciones específicas por tipo
        switch ($documentType) {
            case 'coc':
                $verification['checks']['has_vin'] = preg_match('/[A-Z0-9]{17}/', $text);
                $verification['checks']['has_make'] = preg_match('/(BMW|Mercedes|Audi|Volkswagen|Seat|Skoda)/i', $text);
                break;

            case 'invoice':
                $verification['checks']['has_price'] = preg_match('/\d+,\d{2}/', $text) || preg_match('/\$\d+/', $text);
                $verification['checks']['has_seller'] = preg_match('/(vendedor|seller)/i', $text);
                break;
        }

        $verification['passed'] = !in_array(false, $verification['checks'], true);

        return $verification;
    }
}
