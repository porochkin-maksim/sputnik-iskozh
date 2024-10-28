<?php declare(strict_types=1);

namespace Core\Notifications\Email;

class Emails
{
    public const POROCHKIN_MAKSIM  = 'porochkinmi@mail.ru';
    public const JIGANOVA_SVETLANA = 'sveta_economist@mail.ru';
    public const BRYSKINA_TATYANA  = 'bryskina17@yandex.ru';

    public static function pressAddresses(): array
    {
        return [
            self::POROCHKIN_MAKSIM,
            self::JIGANOVA_SVETLANA,
            self::BRYSKINA_TATYANA,
            env('MAIL_PRESS'),
        ];
    }
}