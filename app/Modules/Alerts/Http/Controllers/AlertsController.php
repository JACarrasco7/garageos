<?php

namespace App\Modules\Alerts\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AlertsController
{
    public function index(Request $request)
    {
        $user = $request->user();

        return Inertia::render('Alerts/Index', [
            'alerts' => $user?->notifications()->latest()->paginate(10),
        ]);
    }

    public function mobileIndex(Request $request): Response
    {
        $user = $request->user();

        $alerts = $user?->alertRules()->with('vehicle')->where('is_active', true)->get();

        return Inertia::render('Mobile/Alerts/Index', [
            'alerts' => $alerts,
        ]);
    }
}
