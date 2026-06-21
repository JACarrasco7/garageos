<?php

namespace App\Http\Controllers;

use App\Modules\Vehicle\Models\Vehicle;
use App\Modules\Alerts\Models\AlertRule;
use App\Modules\Alerts\Models\Notification;
use App\Modules\Documents\Models\Document;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();

        $vehicles = Vehicle::whereHas('garage', fn($q) => $q->where('user_id', $user->id))
            ->with('specs')
            ->limit(5)
            ->get();

        $alertRules = AlertRule::with('vehicle')
            ->whereHas('vehicle.garage', fn($q) => $q->where('user_id', $user->id))
            ->where('is_active', true)
            ->whereNull('last_triggered')
            ->limit(5)
            ->get();

        $alerts = $alertRules->map(fn($rule) => [
            'id' => $rule->id,
            'type' => $rule->type,
            'title' => ucfirst($rule->type) . ' - ' . $rule->vehicle->brand . ' ' . $rule->vehicle->model,
            'body' => $rule->trigger_date
                ? 'Vence: ' . $rule->trigger_date->format('d/m/Y')
                : 'Próximo mantenimiento',
            'read_at' => null,
        ]);

        $notifications = Notification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

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
            'notifications' => $notifications,
            'stats' => $stats,
        ]);
    }
}
