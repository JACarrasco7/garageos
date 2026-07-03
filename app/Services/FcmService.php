<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FcmService
{
    protected $messaging;

    public function __construct()
    {
        $firebase = (new Factory)
            ->withServiceAccount(storage_path('firebase-credentials.json'))
            ->createMessaging();

        $this->messaging = $firebase;
    }

    public function send($token, $title, $body, array $data = [])
    {
        $notification = Notification::create($title, $body);

        $message = CloudMessage::withTarget('token', $token)
            ->withNotification($notification);

        if (! empty($data)) {
            $message = $message->withData($data);
        }

        return $this->messaging->send($message);
    }

    public function sendMulticast($tokens, $title, $body, array $data = [])
    {
        $notification = Notification::create($title, $body);

        $message = CloudMessage::new()
            ->withNotification($notification);

        if (! empty($data)) {
            $message = $message->withData($data);
        }

        return $this->messaging->sendMulticast($message, $tokens);
    }

    public function subscribeToTopic($tokens, $topic)
    {
        return $this->messaging->subscribeToTopic($tokens, $topic);
    }

    public function unsubscribeFromTopic($tokens, $topic)
    {
        return $this->messaging->unsubscribeFromTopic($tokens, $topic);
    }
}
