<?php

namespace App\Http\Controllers;

use App\Modules\Vehicle\Models\Vehicle;
use Inertia\Inertia;
use Inertia\Response;

class WorkshopDashboardController extends Controller
{
    public function index(): Response
    {
        $workshop = auth()->user()->workshop;

        $vehicles = Vehicle::whereHas('maintenanceEntries', function ($q) use ($workshop) {
            $q->where('workshop_id', $workshop->id);
        })
            ->with('garage.user')
            ->latest('updated_at')
            ->get()
            ->map(fn ($v) => [
                'id' => $v->id,
                'plate' => $v->plate,
                'brand' => $v->brand,
                'model' => $v->model,
                'year' => $v->year,
                'current_km' => $v->current_km,
                'owner' => ['name' => $v->garage->user->name],
            ]);

        return Inertia::render('Workshop/Dashboard', [
            'vehicles' => $vehicles,
        ]);
    }
}
