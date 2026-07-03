<?php

namespace App\Modules\Alerts\Jobs;

use App\Models\User;
use App\Modules\Alerts\Models\AlertRule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPushJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public AlertRule $rule
    ) {}

    public function handle(): void
    {
        if (! $this->user->fcm_token) {
            return;
        }

        try {
            $messaging = app('firebase.messaging');
            $messaging->send([
                'token' => $this->user->fcm_token,
                'notification' => [
                    'title' => 'Alerta: '.ucfirst($this->rule->type),
                    'body' => $this->rule->trigger_date
                        ? 'Vence: '.$this->rule->trigger_date->format('d/m/Y')
                        : 'Próximo mantenimiento',
                ],
                'data' => [
                    'vehicle_id' => (string) $this->rule->vehicle_id,
                    'type' => $this->rule->type,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('FCM send failed', [
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
