<?php declare(strict_types=1);

namespace Core\Services\Translations\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TranslationRequired extends Notification// implements ShouldQueue
{
    // use Queueable;

    public function __construct(
        private readonly string $key,
    )
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Необходимо добавить перевод')
            ->line($this->key)
            ->greeting(' ')
            ->salutation(' ');
    }
}
