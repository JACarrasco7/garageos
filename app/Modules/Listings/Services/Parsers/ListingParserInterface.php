<?php

namespace App\Modules\Listings\Services\Parsers;

interface ListingParserInterface
{
    /**
     * Determine if this parser can handle the given HTML content.
     */
    public function canParse(string $html): bool;

    /**
     * Parse the HTML content and return an array of extracted data.
     *
     * @return array<string, mixed>
     */
    public function parse(string $html): array;

    /**
     * Get a unique identifier for this parser.
     */
    public function getIdentifier(): string;
}
