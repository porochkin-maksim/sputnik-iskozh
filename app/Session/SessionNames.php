<?php declare(strict_types=1);

namespace App\Session;

enum SessionNames: string
{
    public const string ACCOUNT_ID = 'account_id';
    public const string IS_ANDROID = 'is_android';
}
