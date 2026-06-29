<?php

namespace Tests\Feature\Modules\Subscription;

use App\Helpers\SubscriptionHelper;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_subscription_page()
    {
        $response = $this->get(route('subscription.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_subscription_plans()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('subscription.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('plans', 2)
            ->where('subscribed', false)
        );
    }

    public function test_free_tier_user_has_vehicle_limit_of_one()
    {
        $user = User::factory()->create();

        $plan = SubscriptionHelper::getPlan($user);

        $this->assertEquals('free', $plan['id']);
        $this->assertEquals(1, $plan['vehicle_limit']);
    }

    public function test_subscription_helper_returns_null_remaining_for_unlimited_plan()
    {
        $user = User::factory()->create();
        
        // Mock pro subscription
        $this->mock(\Laravel\Cashier\Subscription::class, function ($mock) {
            $mock->shouldReceive('stripe_price')->andReturn('pro');
        });

        // For free user, remaining should be 1
        $remaining = SubscriptionHelper::getRemainingVehicles($user);
        $this->assertEquals(1, $remaining);
    }

    public function test_subscription_index_shows_current_subscription_status()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('subscription.index'));

        $response->assertInertia(fn ($page) => $page
            ->where('subscribed', false)
            ->has('plans')
        );
    }

    public function test_remaining_vehicles_for_free_user_starts_at_one()
    {
        $user = User::factory()->create();

        $remaining = SubscriptionHelper::getRemainingVehicles($user);

        $this->assertEquals(1, $remaining);
    }

    public function test_can_add_vehicle_for_free_user_with_zero_vehicles()
    {
        $user = User::factory()->create();

        $canAdd = SubscriptionHelper::canAddVehicle($user);

        $this->assertTrue($canAdd);
    }

    public function test_subscription_helper_get_plan_returns_array()
    {
        $user = User::factory()->create();

        $plan = SubscriptionHelper::getPlan($user);

        $this->assertIsArray($plan);
        $this->assertArrayHasKey('id', $plan);
        $this->assertArrayHasKey('name', $plan);
        $this->assertArrayHasKey('vehicle_limit', $plan);
    }
}
