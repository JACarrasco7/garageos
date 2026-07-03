<?php

namespace App\Modules\Alerts\Listeners;

use App\Modules\Alerts\Events\AlertTriggered;
use App\Modules\Alerts\Jobs\SendPushJob;
use App\Modules\Alerts\Models\Notification;

class SendNotification
{
    public function handle(AlertTriggered $event): void
    {
        $rule = $event->rule;
        $vehicle = $rule->vehicle;
        $user = $vehicle->garage->user;

        // Crear notificación in-app
        Notification::create([
            'user_id' => $user->id,
            'vehicle_id' => $vehicle->id,
            'type' => $rule->type,
            'title' => ucfirst($rule->type).' - '.$vehicle->brand.' '.$vehicle->model,
            'body' => $this->getMessage($rule, $vehicle),
            'channel' => 'in_app',
        ]);

        // Enviar push notification (queued)
        SendPushJob::dispatch($user, $rule);
    }

    private function getMessage($rule, $vehicle): string
    {
        if ($rule->trigger_date) {
            $days = now()->diffInDays($rule->trigger_date, false);

            return "El {$rule->type} del {$vehicle->brand} {$vehicle->model} vence en {$days} días";
        }

        if ($rule->trigger_km) {
            $remaining = $rule->trigger_km - $vehicle->current_km;

            return "El {$vehicle->brand} {$vehicle->model} necesita {$rule->type} en {$remaining} km";
        }

        return "Mantenimiento pendiente: {$rule->type}";
    }
}
