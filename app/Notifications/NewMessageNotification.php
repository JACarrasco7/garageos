<?php

namespace App\Notifications;

use App\Modules\Messaging\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Message $message,
    ) {}

    public function via($notifiable): array
    {
        return ['database', 'fcm'];
    }

    public function toFcm($notifiable): array
    {
        $sender = $this->message->sender->name;
        $preview = strlen($this->message->content) > 50
            ? substr($this->message->content, 0, 50).'...'
            : $this->message->content;

        return [
            'title' => "Nuevo mensaje de {$sender}",
            'body' => $preview,
            'icon' => asset('images/icon.png'),
            'data' => [
                'type' => 'message',
                'conversation_id' => $this->message->conversation_id,
                'message_id' => $this->message->id,
            ],
            'sound' => 'default',
        ];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'message',
            'conversation_id' => $this->message->conversation_id,
            'message_id' => $this->message->id,
            'sender' => $this->message->sender->name,
            'content' => $this->message->content,
        ];
    }
}
