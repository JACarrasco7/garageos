<?php

use App\Models\User;
use App\Modules\Identity\Models\Garage;
use App\Modules\VehicleImport\Models\VehicleImport;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->garage = Garage::factory()->create(['user_id' => $this->user->id]);
});

test('user can get valuation for vehicle import', function () {
    $import = VehicleImport::factory()->create([
        'user_id' => $this->user->id,
        'brand' => 'BMW',
        'model' => '320i',
        'year' => 2019,
        'co2_emissions' => 160,
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('import.valuation', $import));

    $response->assertOk()
        ->assertJsonStructure([
            'valuation' => ['estimated_value', 'min_value', 'max_value'],
            'taxes' => ['catalog_value', 'iedmt', 'itp_estimate', 'co2_rate'],
            'total_cost',
        ]);
});

test('user can generate import certificate', function () {
    $import = VehicleImport::factory()->create([
        'user_id' => $this->user->id,
        'brand' => 'BMW',
        'model' => '320i',
        'year' => 2019,
        'co2_emissions' => 160,
    ]);

    $response = $this->actingAs($this->user)
        ->post(route('import.certificate', $import));

    $response->assertOk()
        ->assertJsonStructure(['certificate_path', 'certificate_url']);
});

test('cannot access valuation of other user import', function () {
    $otherUser = User::factory()->create();
    $import = VehicleImport::factory()->create([
        'user_id' => $otherUser->id,
        'brand' => 'BMW',
        'model' => '320i',
        'year' => 2019,
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('import.valuation', $import));

    $response->assertForbidden();
});

test('calculate import taxes returns correct structure', function () {
    $import = VehicleImport::factory()->create([
        'user_id' => $this->user->id,
        'brand' => 'BMW',
        'model' => '320i',
        'year' => 2019,
        'co2_emissions' => 160,
    ]);

    $taxes = $import->calculateImportTaxes(18000);

    expect($taxes)->toHaveKey('catalog_value')
        ->toHaveKey('iedmt')
        ->toHaveKey('itp_estimate')
        ->toHaveKey('co2_rate')
        ->toHaveKey('depreciation_coefficient');
});

test('low co2 emissions results in zero iedmt', function () {
    $import = VehicleImport::factory()->create([
        'user_id' => $this->user->id,
        'brand' => 'Toyota',
        'model' => 'Prius',
        'year' => 2020,
        'co2_emissions' => 100,
    ]);

    $taxes = $import->calculateImportTaxes(20000);

    expect($taxes['co2_rate'])->toBe(0.00)
        ->and($taxes['iedmt'])->toBe(0.0);
});
