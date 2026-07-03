<?php

namespace App\Notifications;

use App\Modules\Marketplace\Models\MarketplaceListing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class OfferReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public MarketplaceListing $listing,
        public float $offerAmount,
    ) {}

    public function via($notifiable): array
    {
        return ['database', 'fcm', 'mail'];
    }

    public function toFcm($notifiable): array
    {
        return [
            'title' => '¡Nueva oferta para tu vehículo!',
            'body' => "Alguien ofreció {$offerAmount}€ por {$this->listing->title}",
            'icon' => asset('images/icon.png'),
            'data' => [
                'type' => 'offer',
                'listing_id' => $this->listing->id,
                'amount' => $this->offerAmount,
            ],
            'sound' => 'default',
        ];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'offer',
            'listing_id' => $this->listing->id,
            'amount' => $this->offerAmount,
            'title' => $this->listing->title,
        ];
    }
}
