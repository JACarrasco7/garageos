<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SearchAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $alertName,
        public int $newListingsCount,
        public array $listingIds = [],
    ) {}

    public function via($notifiable): array
    {
        return ['database', 'fcm'];
    }

    public function toFcm($notifiable): array
    {
        return [
            'title' => '¡Nuevos anuncios encontrados!',
            'body' => "{$this->newListingsCount} nuevos anuncios coinciden con '{$this->alertName}'",
            'icon' => asset('images/icon.png'),
            'data' => [
                'type' => 'search_alert',
                'listing_ids' => $this->listingIds,
                'count' => $this->newListingsCount,
            ],
            'sound' => 'default',
        ];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'search_alert',
            'alert_name' => $this->alertName,
            'count' => $this->newListingsCount,
            'listing_ids' => $this->listingIds,
        ];
    }
}
