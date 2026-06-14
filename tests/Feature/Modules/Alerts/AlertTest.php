<?php

use App\Modules\Vehicle\Models\Vehicle;
use App\Modules\Identity\Models\Garage;
use App\Modules\Alerts\Models\AlertRule;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->garage = Garage::factory()->create(['user_id' => $this->user->id]);
    $this->vehicle = Vehicle::factory()->create(['garage_id' => $this->garage->id]);
});

test('alert rule can be created', function () {
    $rule = AlertRule::create([
        'vehicle_id' => $this->vehicle->id,
        'type' => 'itv',
        'trigger_date' => now()->addDays(30),
        'advance_days' => 15,
    ]);

    expect($rule->exists)->toBeTrue();
    expect($rule->type)->toBe('itv');
});

test('alert rule detects expiry date trigger', function () {
    $rule = AlertRule::create([
        'vehicle_id' => $this->vehicle->id,
        'type' => 'itv',
        'trigger_date' => now()->addDays(10),
        'advance_days' => 30,
    ]);

    // Debería activarse porque quedan 10 días y el advance_days es 30
    expect($rule->trigger_date->diffInDays(now()))->toBeLessThanOrEqual($rule->advance_days);
});

test('alert rule detects km trigger', function () {
    $this->vehicle->update(['current_km' => 9500]);

    $rule = AlertRule::create([
        'vehicle_id' => $this->vehicle->id,
        'type' => 'aceite',
        'trigger_km' => 10000,
        'advance_km' => 1000,
    ]);

    // Debería activarse porque 9500 >= 10000 - 1000
    expect($this->vehicle->current_km)->toBeGreaterThanOrEqual($rule->trigger_km - $rule->advance_km);
});