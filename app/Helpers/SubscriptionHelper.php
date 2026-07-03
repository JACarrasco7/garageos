<?php

namespace App\Helpers;

use App\Models\User;

class SubscriptionHelper
{
    public static function getPlan(User $user): ?array
    {
        if (! $user->subscribed('default')) {
            return [
                'id' => 'free',
                'name' => 'Gratuito',
                'vehicle_limit' => 1,
                'features' => ['1 vehículo', 'Alertas básicas'],
            ];
        }

        $priceId = $user->subscription('default')->stripe_price;
        $plan = config("subscription.plans.{$priceId}");

        return $plan ? array_merge($plan, ['id' => $priceId]) : null;
    }

    public static function canAddVehicle(User $user): bool
    {
        $plan = self::getPlan($user);

        if ($plan['vehicle_limit'] === null) {
            return true; // Unlimited
        }

        $currentCount = $user->garages()->withCount('vehicles')->get()->sum('vehicles_count');

        return $currentCount < $plan['vehicle_limit'];
    }

    public static function getRemainingVehicles(User $user): ?int
    {
        $plan = self::getPlan($user);

        if ($plan['vehicle_limit'] === null) {
            return null; // Unlimited
        }

        $currentCount = $user->garages()->withCount('vehicles')->get()->sum('vehicles_count');

        return max(0, $plan['vehicle_limit'] - $currentCount);
    }
}
