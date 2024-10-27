<?php

namespace Core\Domains\Proposal\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class ProposalNotification extends Notification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(
        private readonly string $text,
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
        $result = (new MailMessage)->subject('Новое предложение');

        $result->line(new HtmlString(nl2br($this->text)));

        return $result;
    }
}
