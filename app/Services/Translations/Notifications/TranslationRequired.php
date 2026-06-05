<?php declare(strict_types=1);

namespace App\Services\Translations\Notifications;

use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use InvalidArgumentException;

class TranslationRequired extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $key,
    )
    {
        if (empty($this->key)) {
            throw new InvalidArgumentException('Translation key cannot be empty');
        }
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Необходимо добавить перевод')
            ->greeting(' ')
            ->salutation(' ')
            ->line('Требуется добавить перевод для следующего ключа:')
            ->line("🔑 Ключ: {$this->key}")
        ;
    }
}
