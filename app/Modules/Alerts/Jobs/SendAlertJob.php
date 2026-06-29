<?php

namespace App\Modules\Alerts\Jobs;

use App\Modules\Alerts\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendAlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Notification $notification
    ) {}

    public function handle(): void
    {
        $user = $this->notification->user;

        // Enviar email
        Mail::raw($this->notification->body, function ($message) use ($user) {
            $message->to($user->email)
                ->subject($this->notification->title);
        });

        $this->notification->update(['sent_at' => now()]);
    }
}