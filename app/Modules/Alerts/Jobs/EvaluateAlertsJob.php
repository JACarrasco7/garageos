<?php

namespace App\Modules\Alerts\Jobs;

use App\Modules\Alerts\Models\AlertRule;
use App\Modules\Alerts\Events\AlertTriggered;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EvaluateAlertsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $rules = AlertRule::with('vehicle')
            ->active()
            ->get();

        foreach ($rules as $rule) {
            $shouldTrigger = false;

            // Evaluar por fecha
            if ($rule->trigger_date) {
                $daysUntil = now()->diffInDays($rule->trigger_date, false);
                if ($daysUntil <= $rule->advance_days && $daysUntil >= 0) {
                    $shouldTrigger = true;
                }
            }

            // Evaluar por km
            if ($rule->trigger_km && $rule->vehicle->current_km >= $rule->trigger_km - $rule->advance_km) {
                $shouldTrigger = true;
            }

            if ($shouldTrigger && !$rule->last_triggered) {
                $rule->update(['last_triggered' => now()]);
                event(new AlertTriggered($rule));
            }
        }
    }
}
