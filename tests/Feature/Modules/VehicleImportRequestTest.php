<?php

namespace Tests\Feature\Modules;

use App\Models\User;
use App\Modules\VehicleImport\Models\VehicleImportOffer;
use App\Modules\VehicleImport\Models\VehicleImportRequest;
use Tests\TestCase;

class VehicleImportRequestTest extends TestCase
{
    public function test_cliente_puede_crear_solicitud(): void
    {
        $user = $this->createUserWithSubscription('pro');

        // Bypass subscription middleware for this test
        $response = $this->actingAs($user)
            ->post(route('vehicle-import.requests.store'), [
                'brand' => 'BMW',
                'model' => 'Serie 3',
                'year' => 2022,
                'status' => 'open',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('vehicle_import_requests', [
            'user_id' => $user->id,
            'brand' => 'BMW',
            'model' => 'Serie 3',
        ]);
    }

    public function test_solicitud_tiene_ofertas_relacionadas(): void
    {
        $user = $this->createUserWithRole('user');
        $request = VehicleImportRequest::factory()->create(['user_id' => $user->id]);
        $offer = VehicleImportOffer::factory()->create(['vehicle_import_request_id' => $request->id]);

        $this->assertTrue($request->fresh()->offers->contains($offer));
    }

    public function test_oferta_aceptada_cierra_solicitud(): void
    {
        $user = $this->createUserWithRole('user');
        $this->actingAs($user);

        $request = VehicleImportRequest::factory()->create(['user_id' => $user->id]);
        $offer = VehicleImportOffer::factory()->create(['vehicle_import_request_id' => $request->id, 'status' => 'pending']);

        $this->post(route('vehicle-import.offers.accept', $offer));

        $this->assertDatabaseHas('vehicle_import_offers', ['id' => $offer->id, 'status' => 'accepted']);
        $this->assertDatabaseHas('vehicle_import_requests', ['id' => $offer->vehicle_import_request_id, 'status' => 'closed']);
    }
}
