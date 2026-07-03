<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\CloudMessage;

class FcmChannel
{
    public function send($notifiable, Notification $notification)
    {
        $fcmToken = $notifiable->fcm_token;

        if (! $fcmToken) {
            return;
        }

        $message = $notification->toFcm($notifiable);

        $this->sendPushNotification($fcmToken, $message);
    }

    protected function sendPushNotification($token, $message)
    {
        $firebase = app('firebase');

        try {
            $messaging = $firebase->createMessaging();
            $notification = \Kreait\Firebase\Messaging\Notification::create(
                $message['title'],
                $message['body']
            );

            $messageInstance = CloudMessage::withTarget('token', $token)
                ->withNotification($notification);

            if (! empty($message['data'])) {
                $messageInstance = $messageInstance->withData($message['data']);
            }

            if (! empty($message['sound'])) {
                $messageInstance = $messageInstance->withAndroidConfig(
                    AndroidConfig::fromArray([
                        'notification' => [
                            'sound' => $message['sound'],
                        ],
                    ])
                );
            }

            $messaging->send($messageInstance);
        } catch (\Exception $e) {
            \Log::error('FCM notification failed', [
                'error' => $e->getMessage(),
                'token' => $token,
            ]);
        }
    }
}
