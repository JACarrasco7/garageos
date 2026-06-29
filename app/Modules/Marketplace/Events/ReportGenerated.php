<?php

namespace App\Modules\Marketplace\Events;

use App\Modules\Marketplace\Models\SaleReport;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReportGenerated
{
    use Dispatchable, SerializesModels;

    public function __construct(public SaleReport $report) {}
}
