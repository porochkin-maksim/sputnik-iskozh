<?php declare(strict_types=1);

namespace App\Models\Account;

abstract class AccountToUser
{
    public const TABLE = 'account_to_user';

    public const ACCOUNT = 'account';
    public const USER    = 'user';
}
