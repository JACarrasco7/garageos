<?php

namespace App\Modules\Listings\Services\Parsers;

class OpenGraphParser implements ListingParserInterface
{
    /**
     * Determine if this parser can handle the given HTML content.
     */
    public function canParse(string $html): bool
    {
        return str_contains($html, 'property="og:title"')
            || str_contains($html, 'property="og:image"')
            || str_contains($html, 'property="og:description"')
            || str_contains($html, 'og:title')
            || str_contains($html, 'og:image')
            || str_contains($html, 'og:description');
    }

    public function getIdentifier(): string
    {
        return 'opengraph';
    }

    /**
     * Parse the HTML content and return an array of extracted data.
     *
     * @return array<string, mixed>
     */
    public function parse(string $html): array
    {
        $data = [];

        // Using DOMDocument for parsing
        $dom = new \DOMDocument;
        // Suppress errors due to malformed HTML
        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new \DOMXPath($dom);

        // Extract Title
        $title = $xpath->query('//meta[@property="og:title"]/@content')->item(0)?->nodeValue;
        if ($title) {
            $data['title'] = $title;
        }

        // Extract Description
        $description = $xpath->query('//meta[@property="og:description"]/@content')->item(0)?->nodeValue;
        if ($description) {
            $data['description'] = $description;
        }

        // Extract Image
        $image = $xpath->query('//meta[@property="og:image"]/@content')->item(0)?->nodeValue;
        if ($image) {
            $data['image'] = $image;
        }

        // Extract Price (if available in og:price:amount)
        $price = $xpath->query('//meta[@property="product:price:amount"]/@content')->item(0)?->nodeValue
                 ?? $xpath->query('//meta[@property="og:price:amount"]/@content')->item(0)?->nodeValue;
        if ($price) {
            $data['price'] = (float) $price;
        }

        // Extract Currency
        $currency = $xpath->query('//meta[@property="product:price:currency"]/@content')->item(0)?->nodeValue
                    ?? $xpath->query('//meta[@property="og:price:currency"]/@content')->item(0)?->nodeValue;
        if ($currency) {
            $data['currency'] = $currency;
        }

        return $data;
    }
}
