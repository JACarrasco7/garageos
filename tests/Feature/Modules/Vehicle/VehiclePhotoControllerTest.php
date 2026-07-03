<?php

namespace App\Modules\Vehicle\Tests\Feature;

use App\Models\User;
use App\Modules\Identity\Models\Garage;
use App\Modules\Vehicle\Models\Vehicle;
use App\Modules\Vehicle\Models\VehiclePhoto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    Storage::fake('public');

    $this->user = User::factory()->create();
    $this->garage = Garage::factory()->create(['user_id' => $this->user->id]);
    $this->vehicle = Vehicle::factory()->create(['garage_id' => $this->garage->id]);
});

it('can list vehicle photos', function () {
    VehiclePhoto::factory()->count(3)->create(['vehicle_id' => $this->vehicle->id]);

    actingAs($this->user, 'web')
        ->get(route('vehicles.photos.index', $this->vehicle->id))
        ->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('Vehicle/VehiclePhotos')
            ->has('photos', 3)
        );
});

it('can upload photo', function () {
    $file = UploadedFile::fake()->image('test.jpg');

    actingAs($this->user, 'web')
        ->post(route('vehicles.photos.store', $this->vehicle->id), [
            'files' => [$file],
            'category' => 'principal',
            'caption' => 'Test photo',
        ])
        ->assertStatus(200);

    $this->assertDatabaseHas('vehicle_photos', [
        'vehicle_id' => $this->vehicle->id,
        'category' => 'principal',
        'caption' => 'Test photo',
    ]);

    Storage::disk('public')->assertExists("vehicles/{$this->vehicle->id}/photos");
});

it('cannot upload more than 10 photos at once', function () {
    $files = collect(range(1, 11))->map(fn () => UploadedFile::fake()->image('test.jpg'))->toArray();

    actingAs($this->user, 'web')
        ->post(route('vehicles.photos.store', $this->vehicle->id), [
            'files' => $files,
            'category' => 'principal',
        ])
        ->assertSessionHasErrors('files');
});

it('can update photo metadata', function () {
    $photo = VehiclePhoto::factory()->create([
        'vehicle_id' => $this->vehicle->id,
        'category' => 'principal',
        'caption' => 'Old caption',
    ]);

    actingAs($this->user, 'web')
        ->put(route('vehicles.photos.update', [$this->vehicle->id, $photo->id]), [
            'category' => 'frontal',
            'caption' => 'New caption',
            'sort_order' => 5,
        ])
        ->assertStatus(200);

    $this->assertDatabaseHas('vehicle_photos', [
        'id' => $photo->id,
        'category' => 'frontal',
        'caption' => 'New caption',
        'sort_order' => 5,
    ]);
});

it('can reorder photos', function () {
    $photos = VehiclePhoto::factory()->count(3)->create(['vehicle_id' => $this->vehicle->id]);
    $photoIds = $photos->pluck('id')->reverse()->values()->toArray();

    actingAs($this->user, 'web')
        ->post(route('vehicles.photos.reorder', $this->vehicle->id), [
            'photo_ids' => $photoIds,
        ])
        ->assertStatus(200);

    $this->assertDatabaseHas('vehicle_photos', [
        'id' => $photoIds[0],
        'sort_order' => 1,
    ]);
});

it('can delete photo', function () {
    Storage::disk('public')->put('vehicles/1/test.jpg', 'test content');

    $photo = VehiclePhoto::factory()->create([
        'vehicle_id' => $this->vehicle->id,
        'file_path' => 'vehicles/1/test.jpg',
    ]);

    actingAs($this->user, 'web')
        ->delete(route('vehicles.photos.destroy', [$this->vehicle->id, $photo->id]))
        ->assertStatus(200);

    $this->assertDatabaseMissing('vehicle_photos', ['id' => $photo->id]);
    Storage::disk('public')->assertMissing('vehicles/1/test.jpg');
});

it('unauthorized_user_cannot_access_other_vehicle_photos', function () {
    $otherUser = User::factory()->create();
    $otherGarage = Garage::factory()->create(['user_id' => $otherUser->id]);
    $otherVehicle = Vehicle::factory()->create(['garage_id' => $otherGarage->id]);

    actingAs($this->user, 'web')
        ->get(route('vehicles.photos.index', $otherVehicle->id))
        ->assertStatus(403);
});
