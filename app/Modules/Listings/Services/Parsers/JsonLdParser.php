<?php

namespace App\Modules\Listings\Services\Parsers;

class JsonLdParser implements ListingParserInterface
{
    /**
     * Determine if this parser can handle the given HTML content.
     *
     * @param string $html
     * @return bool
     */
    public function canParse(string $html): bool
    {
        return str_contains($html, '<script type="application/ld+json">');
    }

    public function getIdentifier(): string
    {
        return 'json_ld';
    }

    /**
     * Parse the HTML content and return an array of extracted data.
     *
     * @param string $html
     * @return array<string, mixed>
     */
    public function parse(string $html): array
    {
        $data = [];

        // Find all JSON-LD blocks
        preg_match_all('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $matches);

        foreach ($matches[1] as $jsonContent) {
            $json = json_decode(trim($jsonContent), true);

            if (!$json) {
                continue;
            }

            // We are looking for a @type that is a Vehicle or Product
            if ($this->isVehicle($json)) {
                $extracted = $this->extractFromVehicle($json);
                if (!empty($extracted)) {
                    return $extracted;
                }
            }
        }

        return [];
    }

    /**
     * Check if the JSON-LD object represents a vehicle.
     *
     * @param array<string, mixed> $json
     * @return bool
     */
    protected function isVehicle(array $json): bool
    {
        $type = $json['@type'] ?? '';
        
        if (is_array($type)) {
            $type = implode('|', $type);
        }

        return str_contains($type, 'Vehicle') || str_contains($type, 'Product');
    }

    /**
     * Extract data from a vehicle JSON-LD object.
     *
     * @param array<string, mixed> $json
     * @return array<string, mixed>
     */
    protected function extractFromVehicle(array $json): array
    {
        $data = [];

        // Brand and Model
        if (isset($json['brand'])) {
            $data['brand'] = $json['brand'];
        }
        if (isset($json['model'])) {
            $data['model'] = $json['model'];
        }

        // Description
        if (isset($json['description'])) {
            $data['description'] = $json['description'];
        }

        // Image
        if (isset($json['image'])) {
            $image = $json['image'];
            $data['image'] = is_array($image) ? ($image['url'] ?? null) : $image;
        }

        // Price and Currency
        if (isset($json['offers'])) {
            $offers = $json['offers'];
            if (is_array($offers)) {
                if (isset($offers['price'])) {
                    $data['price_eur'] = (float) $offers['price'];
                }
                if (isset($offers['priceCurrency'])) {
                    $data['currency'] = $offers['priceCurrency'];
                }
            } elseif (is_object($offers)) {
                // Handle case where offers might be an object
                if (isset($offers->price)) {
                    $data['price_eur'] = (float) $offers->price;
                }
                if (isset($offers->priceCurrency)) {
                    $data['currency'] = $offers->priceCurrency;
                }
            }
        }

        return array_filter($data);
    }
}
