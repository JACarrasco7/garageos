<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('alerts:evaluate')->daily();
        $schedule->command('imports:check-plates')->dailyAt('08:00');
        $schedule->command('backup:run --only-db')->dailyAt('02:00');
        $schedule->command('backup:clean')->dailyAt('03:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}
