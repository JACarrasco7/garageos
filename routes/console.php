<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Modules\Alerts\Jobs\EvaluateAlertsJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(function () {
    EvaluateAlertsJob::dispatch();
})->daily()->description('Evaluate vehicle alert rules');
