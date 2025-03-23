<?php

namespace Core\Domains\User\Notifications;

use Core\Domains\Infra\Tokens\TokenFacade;
use Core\Enums\DateTimeFormat;
use Core\Requests\RequestArgumentsEnum;
use Core\Resources\RouteNames;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InviteNotification extends Notification
{
    public function __construct(
        private string $email,
    )
    {
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Регистрация на сайте')
            ->line(sprintf('Вы получили это письмо, т.к. для вас была создана учётная запись %s на сайте', $notifiable->email))
            ->action('Установить пароль', $this->getUrl())
            ->line(sprintf('Эта ссылка истекает через сутки в %s.', now()->addDay()->format(DateTimeFormat::DATE_TIME_VIEW_FORMAT)))
            ->line('Если вы не запрашивали такой доступ или он вам не интересен, просто проигнорируйте это письмо.')
        ;
    }

    private function getUrl(): string
    {
        $token = TokenFacade::save([
            'email'   => $this->email,
            'expires' => now()->addDay()->format(DateTimeFormat::DATE_TIME_DEFAULT),
        ]);

        return route(RouteNames::PASSWORD_SET, [RequestArgumentsEnum::TOKEN => $token]);
    }
}
