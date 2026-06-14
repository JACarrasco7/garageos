<?php

namespace App\Http\Controllers;

use App\Modules\Vehicle\Models\Vehicle;
use App\Modules\Alerts\Models\AlertRule;
use App\Modules\Documents\Models\Document;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();

        // Obtener vehículos del usuario
        $vehicles = Vehicle::whereHas('garage', fn($q) => $q->where('user_id', $user->id))
            ->with('specs')
            ->limit(5)
            ->get();

        // Obtener alertas pendientes
        $alerts = AlertRule::with('vehicle')
            ->whereHas('vehicle.garage', fn($q) => $q->where('user_id', $user->id))
            ->where('is_active', true)
            ->whereNull('last_triggered')
            ->limit(5)
            ->get()
            ->map(fn($rule) => [
                'id' => $rule->id,
                'type' => $rule->type,
                'title' => ucfirst($rule->type) . ' - ' . $rule->vehicle->brand . ' ' . $rule->vehicle->model,
                'body' => $rule->trigger_date
                    ? 'Vence: ' . $rule->trigger_date->format('d/m/Y')
                    : 'Próximo mantenimiento',
                'read_at' => null,
            ]);

        // Stats
        $stats = [
            'total_vehicles' => Vehicle::whereHas('garage', fn($q) => $q->where('user_id', $user->id))->count(),
            'total_documents' => Document::whereHas('vehicle.garage', fn($q) => $q->where('user_id', $user->id))->count(),
            'pending_alerts' => AlertRule::whereHas('vehicle.garage', fn($q) => $q->where('user_id', $user->id))
                ->where('is_active', true)
                ->whereNull('last_triggered')
                ->count(),
        ];

        return Inertia::render('Dashboard', [
            'vehicles' => $vehicles,
            'alerts' => $alerts,
            'stats' => $stats,
        ]);
    }
}