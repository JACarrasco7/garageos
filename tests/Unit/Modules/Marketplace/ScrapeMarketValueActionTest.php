<?php

use App\Modules\Marketplace\Actions\ScrapeMarketValueAction;
use App\Modules\Vehicle\Models\Vehicle;
use App\Modules\Identity\Models\Garage;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->garage = Garage::factory()->create(['user_id' => $this->user->id]);
});

test('estimates value for Seat', function () {
    $vehicle = Vehicle::factory()->create([
        'garage_id' => $this->garage->id,
        'brand' => 'Seat',
        'model' => 'León',
        'year' => 2020,
        'current_km' => 50000,
    ]);

    $action = new ScrapeMarketValueAction();
    $value = $action->execute($vehicle);

    expect($value)->not->toBeNull();
    expect($value->estimated_value)->toBeGreaterThanOrEqual(1000);
    expect($value->source)->toBe('estimated');
});

test('estimates value decreases with age', function () {
    $vehicleNew = Vehicle::factory()->create([
        'garage_id' => $this->garage->id,
        'brand' => 'Seat',
        'year' => 2023,
        'current_km' => 10000,
    ]);

    $vehicleOld = Vehicle::factory()->create([
        'garage_id' => $this->garage->id,
        'brand' => 'Seat',
        'year' => 2015,
        'current_km' => 10000,
    ]);

    $action = new ScrapeMarketValueAction();
    $valueNew = $action->execute($vehicleNew);
    $valueOld = $action->execute($vehicleOld);

    expect($valueOld->estimated_value)->toBeLessThan($valueNew->estimated_value);
});