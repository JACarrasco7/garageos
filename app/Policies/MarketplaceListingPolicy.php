<?php

namespace App\Policies;

use App\Models\User;
use App\Modules\Marketplace\Models\MarketplaceListing;

class MarketplaceListingPolicy
{
    public function view(User $user, MarketplaceListing $listing): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasVerifiedEmail();
    }

    public function update(User $user, MarketplaceListing $listing): bool
    {
        return $listing->user_id === $user->id;
    }

    public function delete(User $user, MarketplaceListing $listing): bool
    {
        return $listing->user_id === $user->id && $listing->status !== 'sold';
    }

    public function favorite(User $user, MarketplaceListing $listing): bool
    {
        return $user->hasVerifiedEmail();
    }
}
