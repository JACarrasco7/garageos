<?php

namespace App\Console\Commands;

use App\Modules\VehicleImport\Models\VehicleImport;
use App\Notifications\ImportReminderNotification;
use Illuminate\Console\Command;

class SendImportReminders extends Command
{
    protected $signature = 'import:send-reminders';

    protected $description = 'Send reminders for vehicle imports (plate expiry, ITV deadlines, taxes)';

    public function handle(): int
    {
        // Plates expiring in 7 days
        $expiringImports = VehicleImport::where('plates_expiration_date', '>', now())
            ->where('plates_expiration_date', '<=', now()->addDays(7))
            ->whereDoesntHave('notifications', fn ($q) => $q->where('type', ImportReminderNotification::class))
            ->get();

        foreach ($expiringImports as $import) {
            $import->user->notify(new ImportReminderNotification(
                $import,
                'plates_expiring'
            ));
        }

        $this->info("Sent {$expiringImports->count()} plate expiry reminders");

        // ITV deadline approaching (15 days)
        $itvPending = VehicleImport::where('current_step', 'itv')
            ->where('created_at', '>=', now()->subDays(15))
            ->whereDoesntHave('notifications', fn ($q) => $q->where('type', ImportReminderNotification::class))
            ->get();

        foreach ($itvPending as $import) {
            $import->user->notify(new ImportReminderNotification(
                $import,
                'itv_deadline'
            ));
        }

        $this->info("Sent {$itvPending->count()} ITV deadline reminders");

        // Tax payments pending
        $taxPending = VehicleImport::where('current_step', 'taxes')
            ->whereDoesntHave('notifications', fn ($q) => $q->where('type', ImportReminderNotification::class))
            ->get();

        foreach ($taxPending as $import) {
            $import->user->notify(new ImportReminderNotification(
                $import,
                'taxes_pending'
            ));
        }

        $this->info("Sent {$taxPending->count()} tax payment reminders");

        return Command::SUCCESS;
    }
}
