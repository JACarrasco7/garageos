<?php

use App\Modules\Vehicle\Models\Vehicle;
use App\Modules\Identity\Models\Garage;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->garage = Garage::factory()->create(['user_id' => $this->user->id]);
});

test('user can view vehicles list', function () {
    Vehicle::factory()->count(3)->create(['garage_id' => $this->garage->id]);

    $response = $this->actingAs($this->user)
        ->get(route('vehicles.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn($page) => $page->has('vehicles'));
});

test('user can create a vehicle', function () {
    $response = $this->actingAs($this->user)
        ->post(route('vehicles.store'), [
            'garage_id' => $this->garage->id,
            'plate' => '1234ABC',
            'brand' => 'Seat',
            'model' => 'León',
            'year' => 2020,
            'fuel_type' => 'gasolina',
            'current_km' => 50000,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('vehicles', [
        'plate' => '1234ABC',
        'brand' => 'Seat',
        'model' => 'León',
    ]);
});

test('user can view a vehicle', function () {
    $vehicle = Vehicle::factory()->create(['garage_id' => $this->garage->id]);

    $response = $this->actingAs($this->user)
        ->get(route('vehicles.show', $vehicle));

    $response->assertStatus(200);
    $response->assertInertia(fn($page) => $page->has('vehicle'));
});

test('vehicle generates qr_token automatically', function () {
    $vehicle = Vehicle::factory()->create(['garage_id' => $this->garage->id]);

    expect($vehicle->qr_token)->not->toBeNull();
    expect(strlen($vehicle->qr_token))->toBe(64);
});

test('creating vehicle creates default alert rules', function () {
    $this->actingAs($this->user)
        ->post(route('vehicles.store'), [
            'garage_id' => $this->garage->id,
            'plate' => '5678DEF',
            'brand' => 'Renault',
            'model' => 'Megane',
            'year' => 2019,
            'fuel_type' => 'diesel',
            'current_km' => 80000,
        ]);

    $vehicle = Vehicle::where('plate', '5678DEF')->first();

    expect($vehicle->alertRules)->toHaveCount(3);
    expect($vehicle->alertRules->pluck('type')->toArray())->toContain('itv', 'seguro', 'aceite');
});
