<?php declare(strict_types=1);

namespace Core\Contracts;

interface MailSenderInterface
{
    public function send(object $message): void;
}
