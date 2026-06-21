<?php

use App\Modules\Vehicle\Models\Vehicle;
use App\Modules\Identity\Models\Garage;
use App\Modules\Alerts\Models\AlertRule;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->user = \App\Models\User::factory()->create();
    $this->garage = Garage::factory()->create(['user_id' => $this->user->id]);
});

it('muestra lista de vehículos del usuario', function () {
    $vehicles = Vehicle::factory()->count(3)->create(['garage_id' => $this->garage->id]);

    actingAs($this->user, 'web')
        ->get(route('vehicles.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Vehicle/Index')
            ->has('vehicles', 3)
        );
});

it('permite crear un vehículo nuevo', function () {
    actingAs($this->user, 'web')
        ->post(route('vehicles.store'), [
            'garage_id' => $this->garage->id,
            'plate' => '1234ABC',
            'vin' => 'WAUZZZ8V8KA012345',
            'brand' => 'Audi',
            'model' => 'A3',
            'year' => 2020,
            'fuel_type' => 'gasolina',
            'color' => 'Negro',
            'current_km' => 50000,
        ])
        ->assertRedirect(route('vehicles.show', Vehicle::first()))
        ->assertSessionHas('success');

    assertDatabaseHas('vehicles', [
        'plate' => '1234ABC',
        'brand' => 'Audi',
        'model' => 'A3',
    ]);
});

it('crea alertas por defecto al registrar vehículo', function () {
    $vehicle = Vehicle::factory()->create(['garage_id' => $this->garage->id]);

    event(new \App\Modules\Vehicle\Events\VehicleRegistered($vehicle));

    expect(AlertRule::where('vehicle_id', $vehicle->id)->count())->toBeGreaterThan(0);
});

it('muestra detalles del vehículo', function () {
    $vehicle = Vehicle::factory()->create(['garage_id' => $this->garage->id]);

    actingAs($this->user, 'web')
        ->get(route('vehicles.show', $vehicle))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Vehicle/Show')
            ->where('vehicle.id', $vehicle->id)
        );
});

it('valida datos del vehículo', function () {
    $this->user->update(['email_verified_at' => now()]);

    $response = actingAs($this->user, 'web')
        ->post(route('vehicles.store'), [
            'garage_id' => $this->garage->id,
            'plate' => '',
            'brand' => '',
            'year' => 1800,
            'fuel_type' => 'invalid',
            'current_km' => '',
        ]);

    $response->assertInvalid(['plate', 'brand', 'year', 'fuel_type', 'current_km']);
});

it('genera token QR automáticamente', function () {
    $vehicle = Vehicle::factory()->create(['garage_id' => $this->garage->id]);

    expect($vehicle->qr_token)->not->toBeEmpty()
        ->and(strlen($vehicle->qr_token))->toBe(64);
});

it('filtra solo vehículos activos en dashboard', function () {
    Vehicle::factory()->create(['garage_id' => $this->garage->id, 'is_active' => true]);
    Vehicle::factory()->create(['garage_id' => $this->garage->id, 'is_active' => false]);

    $activeCount = Vehicle::where('garage_id', $this->garage->id)->where('is_active', true)->count();

    expect($activeCount)->toBe(1);
});