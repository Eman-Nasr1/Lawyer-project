<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendOtpCode extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $code, public string $purpose = 'reset') {}

    public function via($notifiable){ return ['mail']; }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('رمز التحقق: ' . strtoupper($this->purpose))
            ->greeting('مرحباً ' . ($notifiable->name ?? ''))
            ->line('رمز التحقق الخاص بك هو')
            ->line("**{$this->code}**")
            ->line('صالح لمدة 10 دقائق.');
    }
}

