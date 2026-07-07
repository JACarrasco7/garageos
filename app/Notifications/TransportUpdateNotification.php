<?php

namespace App\Notifications;

use App\Modules\VehicleImport\Models\TransportEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TransportUpdateNotification extends Notification
{
    use Queueable;

    public function __construct(protected TransportEvent $event) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $statusLabels = [
            'pickup_scheduled' => 'Recogida programada',
            'picked_up' => 'Vehículo recogido',
            'in_transit' => 'En tránsito',
            'customs' => 'En aduanas',
            'delivered' => 'Entregado',
        ];

        return (new MailMessage)
            ->subject('Actualización de transporte - GarageOS')
            ->line("Tu vehículo ha sido {$statusLabels[$this->event->status]}.")
            ->line("Ubicación: {$this->event->location}")
            ->line("Fecha: {$this->event->occurred_at->format('d/m/Y H:i')}")
            ->action('Ver detalles', url("/imports/{$this->event->vehicleImport->id}/wizard"));
    }

    public function toArray($notifiable): array
    {
        return [
            'event_id' => $this->event->id,
            'status' => $this->event->status,
            'location' => $this->event->location,
            'vehicle_import_id' => $this->event->vehicle_import_id,
        ];
    }
}
