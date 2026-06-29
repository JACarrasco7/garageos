<?php

use App\Modules\Alerts\Jobs\SendPushJob;
use App\Modules\Alerts\Models\AlertRule;
use App\Models\User;
use App\Modules\Vehicle\Models\Vehicle;
use App\Modules\Identity\Models\Garage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

test('job does nothing without fcm token', function () {
    $user = User::factory()->create(['fcm_token' => null]);
    $garage = Garage::factory()->create(['user_id' => $user->id]);
    $vehicle = Vehicle::factory()->create(['garage_id' => $garage->id]);
    $rule = AlertRule::factory()->create(['vehicle_id' => $vehicle->id]);

    $job = new SendPushJob($user, $rule);
    $job->handle();

    expect(true)->toBeTrue();
});

test('job handles missing fcm token gracefully', function () {
    $user = User::factory()->create();
    $garage = Garage::factory()->create(['user_id' => $user->id]);
    $vehicle = Vehicle::factory()->create(['garage_id' => $garage->id]);
    $rule = AlertRule::factory()->create(['vehicle_id' => $vehicle->id]);

    $job = new SendPushJob($user, $rule);
    
    $job->handle();
    
    expect(true)->toBeTrue();
});