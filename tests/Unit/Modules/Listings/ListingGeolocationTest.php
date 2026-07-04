<?php

use App\Modules\Listings\Models\Listing;
use Illuminate\Support\Facades\DB;

test('distanceFrom calculates km between two points correctly', function () {
    $listing = new Listing(['lat' => 40.4168, 'lng' => -3.7038]); // Madrid

    $distance = $listing->distanceFrom(41.3851, 2.1734); // Barcelona

    expect($distance)->toBeGreaterThan(490);
    expect($distance)->toBeLessThan(520);
});

test('scopeNearby finds listings within radius using bounding box on SQLite', function () {
    Listing::factory()->create([
        'title' => 'Madrid Listing',
        'lat' => 40.4168,
        'lng' => -3.7038,
        'is_active' => true,
    ]);
    Listing::factory()->create([
        'title' => 'Barcelona Listing',
        'lat' => 41.3851,
        'lng' => 2.1734,
        'is_active' => true,
    ]);

    $nearby = Listing::nearby(40.4168, -3.7038, 50)->get();

    expect($nearby)->toHaveCount(1);
    expect($nearby->first()->title)->toBe('Madrid Listing');
});

test('scopeNearby filters inactive listings', function () {
    Listing::factory()->create([
        'title' => 'Active',
        'lat' => 40.4168,
        'lng' => -3.7038,
        'is_active' => true,
    ]);
    Listing::factory()->create([
        'title' => 'Inactive',
        'lat' => 40.4168,
        'lng' => -3.7038,
        'is_active' => false,
    ]);

    $nearby = Listing::nearby(40.4168, -3.7038, 50)->get();

    expect($nearby)->toHaveCount(1);
    expect($nearby->first()->title)->toBe('Active');
});
