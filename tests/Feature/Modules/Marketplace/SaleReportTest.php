<?php

use App\Models\User;
use App\Modules\Identity\Models\Garage;
use App\Modules\Marketplace\Models\SaleReport;
use App\Modules\Vehicle\Models\Vehicle;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->garage = Garage::factory()->create(['user_id' => $this->user->id]);
});

test('user can generate sale report for vehicle', function () {
    $vehicle = Vehicle::factory()->create(['garage_id' => $this->garage->id]);

    $response = $this->actingAs($this->user)
        ->post(route('marketplace.store', $vehicle));

    $response->assertRedirect();
    $this->assertDatabaseHas('sale_reports', [
        'vehicle_id' => $vehicle->id,
    ]);
});

test('public sale report is accessible via token', function () {
    $vehicle = Vehicle::factory()->create(['garage_id' => $this->garage->id]);
    $report = SaleReport::create([
        'vehicle_id' => $vehicle->id,
        'token' => 'test-token-123',
        'score' => 85,
        'is_active' => true,
        'expires_at' => now()->addDays(30)->toDateTimeString(),
    ]);

    $response = $this->get(route('marketplace.public', 'test-token-123'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->has('report'));
});
