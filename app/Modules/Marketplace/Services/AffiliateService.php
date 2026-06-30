<?php

namespace App\Modules\Marketplace\Services;

use App\Modules\Marketplace\Models\AffiliateLink;

class AffiliateService
{
    /**
     * Find affiliate link for a given SKU or product name
     */
    public function findLink(string $sku, ?string $provider = null): ?AffiliateLink
    {
        $query = AffiliateLink::active()->bySku($sku);

        if ($provider) {
            $query->byProvider($provider);
        }

        return $query->first();
    }

    /**
     * Get all active affiliate links for a provider
     */
    public function getLinksByProvider(string $provider, int $limit = 10)
    {
        return AffiliateLink::active()
            ->byProvider($provider)
            ->limit($limit)
            ->get();
    }

    /**
     * Get recommended parts for a service type
     */
    public function getRecommendedParts(string $serviceType): array
    {
        $mapping = [
            'oil_change' => ['filter-oil', 'oil-5w30', 'gasket-drain-plug'],
            'brake_replacement' => ['brake-pads-front', 'brake-disc-front', 'brake-fluid'],
            'timing_belt' => ['timing-belt-kit', 'water-pump', 'tensioner'],
            'tires' => ['tire-205-55-r16', 'tire-valve', 'wheel-alignment'],
        ];

        $skus = $mapping[$serviceType] ?? [];

        $links = [];
        foreach ($skus as $sku) {
            $link = $this->findLink($sku);
            if ($link) {
                $links[] = $link;
            }
        }

        return $links;
    }

    /**
     * Track click on affiliate link
     */
    public function trackClick(AffiliateLink $link): void
    {
        // Placeholder for future analytics tracking
    }
}
