<?php

namespace App\Modules\Alerts\Jobs;

use App\Modules\Alerts\Models\AlertRule;
use App\Models\User;
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
        // TODO: Implementar Firebase Cloud Messaging
        // Por ahora solo log
        \Log::info("Push notification sent", [
            'user_id' => $this->user->id,
            'vehicle_id' => $this->rule->vehicle_id,
            'type' => $this->rule->type,
        ]);
    }
}