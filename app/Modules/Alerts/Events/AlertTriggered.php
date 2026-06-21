<?php

namespace App\Modules\Alerts\Events;

use App\Modules\Alerts\Models\AlertRule;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AlertTriggered
{
    use Dispatchable, SerializesModels;

    public function __construct(public AlertRule $rule)
    {
    }
}
