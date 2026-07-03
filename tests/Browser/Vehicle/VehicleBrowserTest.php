<?php

use App\Models\User;
use App\Modules\Identity\Models\Garage;
use App\Modules\Vehicle\Models\Vehicle;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

uses(DuskTestCase::class);

test('user can create vehicle via browser', function () {
    $user = User::factory()->create();
    $garage = Garage::factory()->create(['user_id' => $user->id]);

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit(route('vehicles.create'))
            ->type('#plate', 'TEST123')
            ->type('#brand', 'Seat')
            ->type('#model', 'León')
            ->type('#year', '2020')
            ->select('#fuel_type', 'gasolina')
            ->press('Guardar')
            ->assertPathIs(route('vehicles.index', absolute: false))
            ->waitForLivewire()
            ->assertSee('Seat León');
    });
});

test('user can view vehicle details', function () {
    $user = User::factory()->create();
    $garage = Garage::factory()->create(['user_id' => $user->id]);
    $vehicle = Vehicle::factory()->create(['garage_id' => $garage->id, 'brand' => 'Toyota', 'model' => 'Corolla']);

    $this->browse(function (Browser $browser) use ($user, $vehicle) {
        $browser->loginAs($user)
            ->visit(route('vehicles.show', $vehicle))
            ->assertSee('Toyota Corolla')
            ->assertSee($vehicle->plate);
    });
});
