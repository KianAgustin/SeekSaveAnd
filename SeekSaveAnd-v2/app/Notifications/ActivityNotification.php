<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Email for new claims / first messages. Sent only when SEEKSAVE_EMAIL_NOTIFICATIONS=true. */
class ActivityNotification extends Notification
{
    public function __construct(
        public string $subject,
        public string $line,
        public string $url,
    ) {}

    public function via(object $notifiable): array
    {
        return config('seeksave.email_notifications') ? ['mail'] : [];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->subject)
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line($this->line)
            ->action('Open SeekSaveAnd', $this->url);
    }
}
