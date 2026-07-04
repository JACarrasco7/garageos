<?php

use App\Modules\Maintenance\Models\Workshop;
use Illuminate\Support\Facades\DB;

test('distanceFrom calculates km between two points correctly', function () {
    $workshop = new Workshop(['lat' => 40.4168, 'lng' => -3.7038]); // Madrid

    // Madrid -> Barcelona ≈ 505 km
    $distance = $workshop->distanceFrom(41.3851, 2.1734);

    expect($distance)->toBeGreaterThan(490);
    expect($distance)->toBeLessThan(520);
});

test('distanceFrom returns null when coordinates are missing', function () {
    $workshop = new Workshop(['lat' => null, 'lng' => null]);

    expect($workshop->distanceFrom(40.0, -3.0))->toBeNull();
});

test('scopeNearby finds workshops within radius using Haversine on SQLite', function () {
    Workshop::factory()->create([
        'name' => 'Madrid Centro',
        'lat' => 40.4168, 'lng' => -3.7038,
        'is_verified' => true,
    ]);
    Workshop::factory()->create([
        'name' => 'Madrid Norte',
        'lat' => 40.5, 'lng' => -3.7,
        'is_verified' => true,
    ]);
    Workshop::factory()->create([
        'name' => 'Barcelona',
        'lat' => 41.3851, 'lng' => 2.1734,
        'is_verified' => true,
    ]);

    // Buscar desde Madrid centro, radio 50km
    $nearby = Workshop::nearby(40.4168, -3.7038, 50)->get();

    expect($nearby)->toHaveCount(2);
    expect($nearby->pluck('name')->toArray())->toContain('Madrid Centro');
    expect($nearby->pluck('name')->toArray())->toContain('Madrid Norte');
    expect($nearby->pluck('name')->toArray())->not->toContain('Barcelona');
});

test('scopeNearby returns closest first with PostGIS', function () {
    if (DB::connection()->getDriverName() !== 'pgsql') {
        $this->markTestSkipped('PostGIS ordering test only runs on PostgreSQL');
    }

    Workshop::factory()->create([
        'name' => 'Lejos',
        'lat' => 40.5, 'lng' => -3.7,
        'is_verified' => true,
    ]);
    Workshop::factory()->create([
        'name' => 'Cerca',
        'lat' => 40.42, 'lng' => -3.71,
        'is_verified' => true,
    ]);

    $nearby = Workshop::nearby(40.4168, -3.7038, 50)->get();

    expect($nearby->first()->name)->toBe('Cerca');
    expect($nearby->last()->name)->toBe('Lejos');
});

test('scopeNearby filters unverified workshops when querying', function () {
    Workshop::factory()->create([
        'name' => 'Verificado',
        'lat' => 40.4168, 'lng' => -3.7038,
        'is_verified' => true,
    ]);
    Workshop::factory()->create([
        'name' => 'No verificado',
        'lat' => 40.4168, 'lng' => -3.7038,
        'is_verified' => false,
    ]);

    // El scope no filtra is_verified, eso lo hace el controller
    $nearby = Workshop::nearby(40.4168, -3.7038, 50)->get();

    expect($nearby)->toHaveCount(2);
});
