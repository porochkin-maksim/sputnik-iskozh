<?php declare(strict_types=1);

namespace App\Models\Account;

abstract class AccountToUser
{
    public const string TABLE = 'account_to_user';

    public const string ACCOUNT  = 'account';
    public const string USER     = 'user';
    public const string FRACTION = 'fraction';
}
