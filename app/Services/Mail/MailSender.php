<?php declare(strict_types=1);

namespace App\Services\Mail;

use Core\Contracts\MailSenderInterface;
use Illuminate\Contracts\Mail\Mailer;

readonly class MailSender implements MailSenderInterface
{
    public function __construct(
        private Mailer $mailer,
    )
    {
    }

    public function send(object $message): void
    {
        $this->mailer->send($message);
    }
}
