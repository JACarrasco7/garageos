<?php

namespace App\Modules\VehicleImport\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\VehicleImport\Models\VehicleImportOffer;
use Inertia\Inertia;
use Inertia\Response;

class PublicVehiclePortfolioController extends Controller
{
    public function show(User $user): Response
    {
        $offers = VehicleImportOffer::where('user_id', $user->id)
            ->accepted()
            ->with(['request' => fn ($q) => $q->select(['id', 'brand', 'model'])])
            ->withCount('ratings')
            ->latest()
            ->paginate(12);

        return Inertia::render('VehicleImport/Public/Portfolio', [
            'provider' => $user->load('workshop'),
            'offers' => $offers,
        ]);
    }
}
