<?php

namespace App\Notifications;

use App\Modules\VehicleImport\Models\VehicleImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ImportReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public VehicleImport $import,
        public string $reminderType,
    ) {}

    public function via($notifiable): array
    {
        return ['database', 'fcm'];
    }

    public function toFcm($notifiable): array
    {
        $messages = [
            'plates_expiring' => '⚠️ ¡Tus placas temporales expiran en 7 días!',
            'itv_deadline' => '📅 Recuerda: debes pasar la ITV en 30 días',
            'taxes_pending' => '💰 Tienes impuestos pendientes de pago',
        ];

        return [
            'title' => 'Recordatorio de Importación',
            'body' => $messages[$this->reminderType] ?? 'Recordatorio de tu importación',
            'icon' => asset('images/icon.png'),
            'data' => [
                'type' => 'import_reminder',
                'import_id' => $this->import->id,
                'reminder_type' => $this->reminderType,
            ],
            'sound' => 'default',
        ];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'import_reminder',
            'import_id' => $this->import->id,
            'reminder_type' => $this->reminderType,
        ];
    }
}
