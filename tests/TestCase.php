<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Run permission migrations
        $this->artisan('migrate')->run();

        // Create roles
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        Role::create(['name' => 'importer', 'guard_name' => 'web']);
        Role::create(['name' => 'user', 'guard_name' => 'web']);
    }

    protected function createUserWithRole(string $role = 'user'): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);
        return $user;
    }

    protected function createUserWithSubscription(string $plan = 'pro'): User
    {
        $user = $this->createUserWithRole($plan === 'importer' ? 'importer' : 'user');

        $user->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test123',
            'stripe_status' => 'active',
            'stripe_price' => $plan,
        ]);

        return $user;
    }

    protected function createUserWithActiveSubscription(string $plan = 'pro'): User
    {
        $user = $this->createUserWithSubscription($plan);

        // Asegurar que el usuario tiene acceso a las funcionalidades
        $user->subscriptions()->update(['stripe_status' => 'active']);

        return $user;
    }
}
