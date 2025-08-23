<?php declare(strict_types=1);

namespace Core\Session;

enum SessionNames: string
{
    public const string COOKIE_AGREEMENT = 'cookie_agreement';
    public const string ACCOUNT_ID       = 'account_id';
}
