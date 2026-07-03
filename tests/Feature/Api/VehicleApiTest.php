<?php

use App\Models\User;
use App\Modules\Identity\Models\Garage;
use App\Modules\Maintenance\Models\ServicePack;
use App\Modules\Vehicle\Models\Vehicle;

use function Pest\Laravel\getJson;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->garage = Garage::factory()->create(['user_id' => $this->user->id]);
});

test('api returns vehicle data by qr token', function () {
    $vehicle = Vehicle::factory()->create(['garage_id' => $this->garage->id]);

    $response = getJson(route('api.vehicles.show', $vehicle->qr_token));

    $response->assertStatus(200)
        ->assertJsonPath('plate', $vehicle->plate)
        ->assertJsonPath('brand', $vehicle->brand)
        ->assertJsonPath('model', $vehicle->model);
});

test('api returns service pack with affiliate links', function () {
    $vehicle = Vehicle::factory()->create(['garage_id' => $this->garage->id]);

    ServicePack::create([
        'maintenance_type' => 'aceite',
        'name' => 'Cambio de aceite básico',
        'items' => [
            ['name' => 'Aceite de motor 5W30', 'reference' => '5L', 'price' => 45.00],
        ],
    ]);

    $response = getJson(route('api.vehicles.service-pack', [$vehicle->qr_token, 'aceite']));

    $response->assertStatus(200)
        ->assertJsonPath('pack.name', 'Cambio de aceite básico')
        ->assertJsonPath('affiliate_links.0.affiliate_url', fn ($url) => str_contains($url, 'autodoc.es'));
});
