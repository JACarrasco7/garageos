<?php

use App\Modules\Alerts\Jobs\EvaluateAlertsJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(function () {
    EvaluateAlertsJob::dispatch();
})->daily()->description('Evaluate vehicle alert rules');
