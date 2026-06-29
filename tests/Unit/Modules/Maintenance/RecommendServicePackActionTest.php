<?php

use App\Modules\Maintenance\Actions\RecommendServicePackAction;
use App\Modules\Maintenance\Models\ServicePack;
use App\Modules\Vehicle\Models\Vehicle;
use App\Modules\Identity\Models\Garage;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $user = User::factory()->create();
    $garage = Garage::factory()->create(['user_id' => $user->id]);
    $this->garage = $garage;
});

test('recommends oil change when due', function () {
    $vehicle = Vehicle::factory()->create([
        'garage_id' => $this->garage->id,
        'current_km' => 25000,
    ]);

    $pack = ServicePack::create([
        'maintenance_type' => 'aceite',
        'name' => 'Cambio de aceite',
        'items' => [['name' => 'Aceite', 'reference' => 'OIL', 'price' => 50]],
    ]);

    $action = new RecommendServicePackAction();
    $recommendation = $action->execute($vehicle);

    expect($recommendation)->not->toBeNull();
    expect($recommendation->maintenance_type)->toBe('aceite');
});

test('returns null when no service needed', function () {
    $vehicle = Vehicle::factory()->create([
        'garage_id' => $this->garage->id,
        'current_km' => 5000,
    ]);

    $action = new RecommendServicePackAction();
    $recommendation = $action->execute($vehicle);

    expect($recommendation)->toBeNull();
});