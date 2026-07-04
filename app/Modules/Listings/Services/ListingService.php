<?php

namespace App\Modules\Listings\Services;

use App\Modules\Listings\Enums\ListingPortal;
use App\Modules\Listings\Models\Listing;
use App\Modules\Listings\Services\Parsers\ListingParserInterface;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ListingService
{
    /**
     * @param  array<ListingParserInterface>  $parsers
     */
    public function __construct(
        protected array $parsers
    ) {}

    /**
     * Get all listings.
     */
    public function all()
    {
        return Listing::query()
            ->with('creator')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Full-text search across listings.
     *
     * @return LengthAwarePaginator
     */
    public function search(string $query, int $perPage = 15)
    {
        return Listing::query()
            ->with('creator')
            ->where('is_active', true)
            ->fullText($query)
            ->paginate($perPage);
    }

    /**
     * Find a listing by ID.
     */
    public function find(int $id): ?Listing
    {
        return Listing::with('creator')->find($id);
    }

    /**
     * Create a new listing.
     */
    public function create(array $data): Listing
    {
        return DB::transaction(function () use ($data) {
            return Listing::create($data);
        });
    }

    /**
     * Update an existing listing.
     */
    public function update(int $id, array $data): Listing
    {
        return DB::transaction(function () use ($id, $data) {
            $listing = $this->find($id);
            if (! $listing) {
                throw new Exception('Listing not found.');
            }
            $listing->update($data);

            return $listing;
        });
    }

    /**
     * Delete a listing.
     */
    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $listing = $this->find($id);
            if (! $listing) {
                return false;
            }

            return $listing->delete();
        });
    }

    /**
     * Orchestrate the extraction process from a URL.
     *
     * This is a placeholder for the actual scraping/parsing logic.
     */
    public function extractFromUrl(string $url, ListingPortal $portal, int $userId): Listing
    {
        // 1. Create a pending listing record
        $listing = $this->create([
            'created_by_user_id' => $userId,
            'source_url' => $url,
            'source_portal' => $portal->value,
            'extraction_status' => 'pending',
            'extraction_method' => 'manual', // Default until parser is implemented
        ]);

        // 2. Perform the actual scraping/parsing
        try {
            $response = Http::get($url);

            if ($response->failed()) {
                $listing->update([
                    'extraction_status' => 'failed',
                    'extraction_error' => "Failed to fetch content from URL: {$url}. Status: {$response->status()}",
                ]);

                return $listing;
            }

            $html = $response->body();

            foreach ($this->parsers as $parser) {
                if ($parser->canParse($html)) {
                    $extractedData = $parser->parse($html);

                    $listing->update(array_merge($extractedData, [
                        'extraction_status' => 'completed',
                        'extraction_method' => $parser->getIdentifier(),
                    ]));

                    return $listing->fresh();
                }
            }

            $listing->update([
                'extraction_status' => 'failed',
                'extraction_error' => 'No suitable parser found for the provided URL.',
            ]);

        } catch (Exception $e) {
            Log::error("Failed to extract listing from URL: {$url}. Error: {$e->getMessage()}");
            $listing->update([
                'extraction_status' => 'failed',
                'extraction_error' => $e->getMessage(),
            ]);
        }

        return $listing->fresh();
    }
}
