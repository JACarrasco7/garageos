<?php

namespace App\Modules\Identity\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Identity\Models\Garage;
use Inertia\Inertia;
use Inertia\Response;

class GarageController extends Controller
{
    public function index(): Response
    {
        $garages = Garage::with('vehicles')
            ->where('user_id', auth()->id())
            ->get();

        return Inertia::render('Dashboard/Index', [
            'garages' => $garages,
        ]);
    }

    public function show(Garage $garage): Response
    {
        $this->authorize('view', $garage);

        return Inertia::render('Garage/Show', [
            'garage' => $garage->load('vehicles.specs'),
        ]);
    }
}
