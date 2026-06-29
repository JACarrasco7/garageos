<?php

use App\Modules\Vehicle\Models\Vehicle;
use App\Modules\Identity\Models\Garage;
use App\Modules\Alerts\Models\AlertRule;
use App\Modules\Alerts\Models\Notification;
use App\Modules\Alerts\Jobs\EvaluateAlertsJob;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\travelTo;

beforeEach(function () {
    $this->user = \App\Models\User::factory()->create();
    $this->garage = Garage::factory()->create(['user_id' => $this->user->id]);
    $this->vehicle = Vehicle::factory()->create(['garage_id' => $this->garage->id, 'current_km' => 50000]);
});

it('crea regla de alerta por defecto al registrar vehículo', function () {
    event(new \App\Modules\Vehicle\Events\VehicleRegistered($this->vehicle));

    assertDatabaseHas('alert_rules', [
        'vehicle_id' => $this->vehicle->id,
        'type' => 'itv',
    ]);

    assertDatabaseHas('alert_rules', [
        'vehicle_id' => $this->vehicle->id,
        'type' => 'seguro',
    ]);

    assertDatabaseHas('alert_rules', [
        'vehicle_id' => $this->vehicle->id,
        'type' => 'aceite',
    ]);
});

it('detecta alerta por kilometraje', function () {
    $vehicle = Vehicle::with('garage.user')->find($this->vehicle->id);

    AlertRule::create([
        'vehicle_id' => $vehicle->id,
        'type' => 'aceite',
        'trigger_km' => 50000,
        'advance_km' => 1000,
        'is_active' => true,
    ]);

    $vehicle->update(['current_km' => 50500]);

    $job = new EvaluateAlertsJob();
    $job->handle();

    expect(Notification::where('vehicle_id', $vehicle->id)->count())->toBe(1);
});

it('detecta alerta por fecha', function () {
    $vehicle = Vehicle::with('garage.user')->find($this->vehicle->id);

    travelTo(now()->addDays(10));

    AlertRule::create([
        'vehicle_id' => $vehicle->id,
        'type' => 'itv',
        'trigger_date' => now()->addDays(15),
        'advance_days' => 15,
        'is_active' => true,
    ]);

    $job = new EvaluateAlertsJob();
    $job->handle();

    expect(Notification::where('vehicle_id', $vehicle->id)->count())->toBe(1);
});

it('marca notificación como leída', function () {
    $notification = Notification::create([
        'user_id' => $this->user->id,
        'vehicle_id' => $this->vehicle->id,
        'type' => 'itv',
        'title' => 'ITV próxima',
        'body' => 'Tu ITV vence pronto',
        'read_at' => null,
    ]);

    $notification->update(['read_at' => now()]);

    expect($notification->fresh()->read_at)->not->toBeNull();
});

it('marca regla como disparada', function () {
    $vehicle = Vehicle::with('garage.user')->find($this->vehicle->id);

    $rule = AlertRule::create([
        'vehicle_id' => $vehicle->id,
        'type' => 'itv',
        'trigger_date' => now()->addDays(5),
        'advance_days' => 10,
        'is_active' => true,
    ]);

    $job = new EvaluateAlertsJob();
    $job->handle();

    expect($rule->fresh()->last_triggered)->not->toBeNull();
});

it('calcula puntuación del vehículo', function () {
    $score = app(\App\Modules\Marketplace\Actions\CalculateScoreAction::class)->execute($this->vehicle);

    expect($score)->toBeInt()->toBeBetween(0, 100);
});
