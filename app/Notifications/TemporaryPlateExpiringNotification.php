<?php

namespace App\Notifications;

use App\Modules\VehicleImport\Models\TemporaryPlate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TemporaryPlateExpiringNotification extends Notification
{
    use Queueable;

    public function __construct(public TemporaryPlate $plate) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Placa temporal próxima a expirar')
            ->line("La placa temporal {$this->plate->plate_number} expirará pronto.")
            ->line("Fecha de expiración: {$this->plate->expires_at->format('d/m/Y')}")
            ->action('Ver importación', route('import.wizard', $this->plate->vehicleImport));
    }

    public function toArray($notifiable): array
    {
        return [
            'plate_id' => $this->plate->id,
            'plate_number' => $this->plate->plate_number,
            'expires_at' => $this->plate->expires_at->toDateString(),
            'import_id' => $this->plate->import_id,
        ];
    }
}
