<?php

namespace App\Modules\Marketplace\Policies;

use App\Models\User;
use App\Modules\Marketplace\Models\MarketplaceListing;

class MarketplaceListingPolicy
{
    public function purchase(User $user, MarketplaceListing $listing): bool
    {
        return $user->id !== $listing->user_id && $listing->status === 'active';
    }

    public function update(User $user, MarketplaceListing $listing): bool
    {
        return $user->id === $listing->user_id;
    }

    public function delete(User $user, MarketplaceListing $listing): bool
    {
        return $user->id === $listing->user_id;
    }
}