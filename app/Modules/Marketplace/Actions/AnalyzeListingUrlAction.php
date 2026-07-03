<?php

namespace App\Modules\Marketplace\Actions;

use Illuminate\Support\Facades\Http;

class AnalyzeListingUrlAction
{
    protected array $supportedDomains = [
        'mobile.de',
        'autoscout24.de',
        'ebay-kleinanzeigen.de',
    ];

    public function execute(string $url): array
    {
        $domain = parse_url($url, PHP_URL_HOST);

        if (! in_array($domain, $this->supportedDomains)) {
            throw new \InvalidArgumentException("Dominio no soportado: {$domain}");
        }

        $html = $this->fetchPage($url);

        return match ($domain) {
            'mobile.de' => $this->parseMobileDe($html, $url),
            'autoscout24.de' => $this->parseAutoscout24($html, $url),
            'ebay-kleinanzeigen.de' => $this->parseEbayKleinanzeigen($html, $url),
            default => throw new \InvalidArgumentException("Parser no implementado para: {$domain}"),
        };
    }

    protected function fetchPage(string $url): string
    {
        $response = Http::withHeaders([
            'User-Agent' => 'GarageOS Bot 1.0 (contact@garageos.com)',
            'Accept' => 'text/html,application/xhtml+xml',
            'Accept-Language' => 'es-ES,es;q=0.9',
        ])->timeout(30)->get($url);

        return $response->body();
    }

    protected function parseMobileDe(string $html, string $url): array
    {
        $data = [];

        // Extract JSON-LD data
        if (preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $matches)) {
            $jsonLd = json_decode($matches[1], true);
            $data['title'] = $jsonLd['name'] ?? null;
            $data['price'] = $jsonLd['offers']['price'] ?? null;
            $data['description'] = $jsonLd['description'] ?? null;
        }

        // Extract Open Graph
        $data['brand'] = $this->extractMetaContent($html, 'og:brand') ?? $this->extractText($html, 'data-brand');
        $data['model'] = $this->extractMetaContent($html, 'og:model') ?? $this->extractText($html, 'data-model');
        $data['year'] = $this->extractMetaContent($html, 'og:year') ?? $this->extractYear($html);
        $data['mileage'] = $this->extractMileage($html);
        $data['fuel_type'] = $this->extractFuelType($html);
        $data['power_hp'] = $this->extractPower($html);

        $data['source_url'] = $url;
        $data['source_domain'] = 'mobile.de';

        return $data;
    }

    protected function parseAutoscout24(string $html, string $url): array
    {
        return $this->parseMobileDe($html, $url);
    }

    protected function parseEbayKleinanzeigen(string $html, string $url): array
    {
        return $this->parseMobileDe($html, $url);
    }

    protected function extractMetaContent(string $html, string $property): ?string
    {
        if (preg_match('/<meta property="'.$property.'" content="([^"]*)"/', $html, $matches)) {
            return $matches[1];
        }

        return null;
    }

    protected function extractText(string $html, string $attribute): ?string
    {
        if (preg_match('/'.$attribute.'="([^"]*)"/', $html, $matches)) {
            return $matches[1];
        }

        return null;
    }

    protected function extractYear(string $html): ?int
    {
        if (preg_match('/\b(19|20)\d{2}\b/', $html, $matches)) {
            return (int) $matches[0];
        }

        return null;
    }

    protected function extractMileage(string $html): ?int
    {
        if (preg_match('/(\d+(?:\.\d+)?)\s*(?:km|Kilometer)/i', $html, $matches)) {
            return (int) str_replace('.', '', $matches[1]);
        }

        return null;
    }

    protected function extractFuelType(string $html): ?string
    {
        $fuelTypes = ['diesel', 'gasoline', 'electric', 'hybrid', 'lpg', 'cng'];
        $htmlLower = strtolower($html);

        foreach ($fuelTypes as $fuel) {
            if (str_contains($htmlLower, $fuel)) {
                return $fuel;
            }
        }

        return null;
    }

    protected function extractPower(string $html): ?int
    {
        if (preg_match('/(\d+)\s*(?:PS|HP|Caballos)/i', $html, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }
}
