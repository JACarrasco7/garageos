<?php

use App\Modules\Vehicle\Models\Vehicle;
use App\Modules\Identity\Models\Garage;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->garage = Garage::factory()->create(['user_id' => $this->user->id]);
});

test('public vehicle view by qr token works without auth', function () {
    $vehicle = Vehicle::factory()->create([
        'garage_id' => $this->garage->id,
        'plate' => 'ABC1234',
        'brand' => 'Seat',
        'model' => 'León',
        'year' => 2020,
    ]);

    $response = $this->get(route('public.vehicles.show', $vehicle->qr_token));

    $response->assertStatus(200);
    $response->assertInertia(fn($page) => $page->has('vehicle'));
});

test('public vehicle view returns 404 for invalid token', function () {
    $response = $this->get(route('public.vehicles.show', 'invalid-token'));

    $response->assertStatus(404);
});
