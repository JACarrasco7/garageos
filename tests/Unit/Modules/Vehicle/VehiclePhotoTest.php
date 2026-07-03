<?php

namespace App\Modules\Vehicle\Tests\Unit;

use App\Models\User;
use App\Modules\Identity\Models\Garage;
use App\Modules\Vehicle\Models\Vehicle;
use App\Modules\Vehicle\Models\VehiclePhoto;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->garage = Garage::factory()->create(['user_id' => $this->user->id]);
    $this->vehicle = Vehicle::factory()->create(['garage_id' => $this->garage->id]);
});

it('vehicle has photos relationship', function () {
    $photo = VehiclePhoto::factory()->create([
        'vehicle_id' => $this->vehicle->id,
    ]);

    expect($this->vehicle->photos->first())->toBeInstanceOf(VehiclePhoto::class)
        ->and($this->vehicle->photos->first()->id)->toBe($photo->id);
});

it('by_category_scope_filters_photos', function () {
    VehiclePhoto::factory()->create([
        'vehicle_id' => $this->vehicle->id,
        'category' => 'principal',
    ]);

    VehiclePhoto::factory()->create([
        'vehicle_id' => $this->vehicle->id,
        'category' => 'averia',
    ]);

    $principalPhotos = $this->vehicle->photos()->byCategory('principal')->get();
    $averiaPhotos = $this->vehicle->photos()->byCategory('averia')->get();

    expect($principalPhotos)->toHaveCount(1)
        ->and($averiaPhotos)->toHaveCount(1)
        ->and($principalPhotos->first()->category)->toBe('principal');
});

it('ordered_scope_sorts_by_sort_order', function () {
    $photo1 = VehiclePhoto::factory()->create([
        'vehicle_id' => $this->vehicle->id,
        'sort_order' => 2,
    ]);

    $photo2 = VehiclePhoto::factory()->create([
        'vehicle_id' => $this->vehicle->id,
        'sort_order' => 1,
    ]);

    $orderedPhotos = $this->vehicle->photos()->ordered()->get();

    expect($orderedPhotos->first()->id)->toBe($photo2->id)
        ->and($orderedPhotos->last()->id)->toBe($photo1->id);
});

it('url_attribute_returns_asset_path', function () {
    $photo = VehiclePhoto::factory()->create([
        'vehicle_id' => $this->vehicle->id,
        'file_path' => 'vehicles/1/test.jpg',
    ]);

    expect($photo->url)->toBe(asset('vehicles/1/test.jpg'));
});

it('thumbnail_url_attribute_returns_thumbnail_path', function () {
    $photo = VehiclePhoto::factory()->create([
        'vehicle_id' => $this->vehicle->id,
        'file_path' => 'vehicles/1/test.jpg',
    ]);

    expect($photo->thumbnail_url)->toBe(asset('vehicles/1/test-thumb.jpg'));
});

it('vehicle_photo_belongs_to_vehicle', function () {
    $photo = VehiclePhoto::factory()->create([
        'vehicle_id' => $this->vehicle->id,
    ]);

    expect($photo->vehicle)->toBeInstanceOf(Vehicle::class)
        ->and($photo->vehicle->id)->toBe($this->vehicle->id);
});

it('category_enum_is_valid', function () {
    $validCategories = [
        'principal',
        'frontal',
        'lateral',
        'trasero',
        'interior',
        'motor',
        'averia',
        'daño',
        'documento',
        'antes_reparacion',
        'despues_reparacion',
    ];

    foreach ($validCategories as $category) {
        $photo = VehiclePhoto::factory()->create([
            'vehicle_id' => $this->vehicle->id,
            'category' => $category,
        ]);

        expect($photo->category)->toBe($category);
    }
});
