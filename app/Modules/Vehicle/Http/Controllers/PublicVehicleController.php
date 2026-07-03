<?php

namespace App\Modules\Vehicle\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Vehicle\Models\Vehicle;
use Inertia\Inertia;
use Inertia\Response;

class PublicVehicleController extends Controller
{
    public function showByQr(string $token): Response
    {
        $vehicle = Vehicle::with(['specs', 'documents' => fn ($q) => $q->where('type', 'itv')])
            ->where('qr_token', $token)
            ->firstOrFail();

        return Inertia::render('Vehicle/Public', [
            'vehicle' => $vehicle,
        ]);
    }
}
