<?php

namespace App\Http\Controllers;

use App\Modules\Alerts\Models\AlertRule;
use App\Modules\Alerts\Models\Notification;
use App\Modules\Documents\Models\Document;
use App\Modules\Identity\Models\Garage;
use App\Modules\Maintenance\Models\MaintenanceEntry;
use App\Modules\Vehicle\Models\Vehicle;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();

        $vehicles = Vehicle::whereHas('garage', fn ($q) => $q->where('user_id', $user->id))
            ->with('specs')
            ->limit(5)
            ->get();

        $alertRules = AlertRule::with('vehicle')
            ->whereHas('vehicle.garage', fn ($q) => $q->where('user_id', $user->id))
            ->where('is_active', true)
            ->whereNull('last_triggered')
            ->limit(5)
            ->get();

        $alerts = $alertRules->map(fn ($rule) => [
            'id' => $rule->id,
            'type' => $rule->type,
            'title' => ucfirst($rule->type).' - '.$rule->vehicle->brand.' '.$rule->vehicle->model,
            'body' => $rule->trigger_date
                ? 'Vence: '.$rule->trigger_date->format('d/m/Y')
                : 'Próximo mantenimiento',
            'read_at' => null,
        ]);

        $notifications = Notification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $stats = [
            'total_vehicles' => Vehicle::whereHas('garage', fn ($q) => $q->where('user_id', $user->id))->count(),
            'total_documents' => Document::whereHas('vehicle.garage', fn ($q) => $q->where('user_id', $user->id))->count(),
            'pending_alerts' => AlertRule::whereHas('vehicle.garage', fn ($q) => $q->where('user_id', $user->id))
                ->where('is_active', true)
                ->whereNull('last_triggered')
                ->count(),
        ];

        return Inertia::render('Dashboard', [
            'vehicles' => $vehicles,
            'alerts' => $alerts,
            'notifications' => $notifications,
            'stats' => $stats,
            'limits' => [
                'max_vehicles' => $user->getVehicleLimit(),
                'current_vehicles' => $user->garages()->withCount('vehicles')->get()->sum('vehicles_count'),
                'subscribed' => $user->subscribed('default'),
                'plan' => $user->subscription('default')?->stripe_price ?? 'free',
            ],
        ]);
    }

    public function stats(): Response
    {
        $user = auth()->user();

        $stats = [
            'total_vehicles' => Vehicle::whereHas('garage', fn ($q) => $q->where('user_id', $user->id))->count(),
            'total_documents' => Document::whereHas('vehicle.garage', fn ($q) => $q->where('user_id', $user->id))->count(),
            'total_maintenance' => MaintenanceEntry::whereHas('vehicle.garage', fn ($q) => $q->where('user_id', $user->id))->count(),
            'total_spent' => (float) MaintenanceEntry::whereHas('vehicle.garage', fn ($q) => $q->where('user_id', $user->id))->sum('cost'),
            'avg_cost_per_km' => $this->calculateAvgCostPerKm($user),
            'monthly_spending' => $this->getMonthlySpending($user),
        ];

        return Inertia::render('Dashboard/Stats', [
            'stats' => $stats,
        ]);
    }

    private function getMonthlySpending($user): array
    {
        return MaintenanceEntry::whereHas('vehicle.garage', fn ($q) => $q->where('user_id', $user->id))
            ->selectRaw('DATE_FORMAT(service_date, "%Y-%m") as month, SUM(cost) as amount')
            ->groupBy('month')
            ->orderBy('month')
            ->limit(12)
            ->get()
            ->map(fn ($row) => ['month' => $row->month, 'amount' => (float) $row->amount])
            ->toArray();
    }

    private function calculateAvgCostPerKm($user): float
    {
        $totalCost = MaintenanceEntry::whereHas('vehicle.garage', fn ($q) => $q->where('user_id', $user->id))->sum('cost');
        $totalKm = Vehicle::whereHas('garage', fn ($q) => $q->where('user_id', $user->id))->avg('current_km');

        return $totalKm > 0 ? round($totalCost / $totalKm, 4) : 0;
    }

    public function mobileIndex(): Response
    {
        $user = auth()->user();

        $vehicles = Vehicle::whereHas('garage', fn ($q) => $q->where('user_id', $user->id))
            ->with('specs')
            ->get();

        $recentEntries = MaintenanceEntry::whereHas('vehicle.garage', fn ($q) => $q->where('user_id', $user->id))
            ->with('vehicle')
            ->latest()
            ->limit(5)
            ->get();

        $documentsCount = Document::whereHas('vehicle.garage', fn ($q) => $q->where('user_id', $user->id))->count();
        $alertsCount = AlertRule::whereHas('vehicle.garage', fn ($q) => $q->where('user_id', $user->id))
            ->where('is_active', true)
            ->count();

        return Inertia::render('Mobile/Dashboard/Index', [
            'vehicles' => $vehicles,
            'recentEntries' => $recentEntries,
            'documentsCount' => $documentsCount,
            'alertsCount' => $alertsCount,
        ]);
    }

    public function mobileProfile(): Response
    {
        $user = auth()->user();

        $garages = Garage::where('user_id', $user->id)->get(['id', 'name']);

        return Inertia::render('Mobile/Profile/Index', [
            'user' => $user,
            'garages' => $garages,
        ]);
    }
}
