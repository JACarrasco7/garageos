<?php

use App\Models\User;
use App\Modules\Identity\Models\Garage;
use App\Modules\VehicleImport\Actions\ImportValuationAction;
use App\Modules\VehicleImport\Models\VehicleImport;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->garage = Garage::factory()->create(['user_id' => $this->user->id]);
});

test('calculates valuation for vehicle import', function () {
    $import = VehicleImport::factory()->create([
        'user_id' => $this->user->id,
        'brand' => 'BMW',
        'model' => '320i',
        'year' => 2019,
        'co2_emissions' => 160,
    ]);

    $action = new ImportValuationAction;
    $result = $action->execute($import);

    expect($result)->toHaveKey('valuation');
    expect($result)->toHaveKey('taxes');
    expect($result)->toHaveKey('total_cost');
    expect($result['valuation']->estimated_value)->toBeGreaterThan(0);
});

test('calculates taxes based on co2 emissions', function () {
    $import = VehicleImport::factory()->create([
        'user_id' => $this->user->id,
        'brand' => 'BMW',
        'model' => '320i',
        'year' => 2019,
        'co2_emissions' => 160,
    ]);

    $action = new ImportValuationAction;
    $result = $action->execute($import);

    expect($result['taxes']['co2_rate'])->toBe(0.0975);
    expect($result['taxes']['iedmt'])->toBeGreaterThan(0);
});

test('zero tax rate for low co2 emissions', function () {
    $import = VehicleImport::factory()->create([
        'user_id' => $this->user->id,
        'brand' => 'Toyota',
        'model' => 'Prius',
        'year' => 2020,
        'co2_emissions' => 100,
    ]);

    $action = new ImportValuationAction;
    $result = $action->execute($import);

    expect($result['taxes']['co2_rate'])->toBe(0.00);
    expect($result['taxes']['iedmt'])->toBe(0.0);
});

test('total cost includes all components', function () {
    $import = VehicleImport::factory()->create([
        'user_id' => $this->user->id,
        'brand' => 'Seat',
        'model' => 'León',
        'year' => 2020,
        'co2_emissions' => 120,
    ]);

    $action = new ImportValuationAction;
    $result = $action->execute($import);

    $expectedTotal = $result['valuation']->estimated_value
        + $result['taxes']['iedmt']
        + $result['taxes']['itp_estimate'];

    expect($result['total_cost'])->toBe($expectedTotal);
});
