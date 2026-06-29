<?php

namespace App\Modules\Maintenance\Events;

use App\Modules\Maintenance\Models\MaintenanceEntry;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RevisionCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(public MaintenanceEntry $entry) {}
}