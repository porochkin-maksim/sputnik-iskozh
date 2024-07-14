<?php declare(strict_types=1);

use Core\Domains\Account\Models\AccountDTO;
use Core\Domains\User\Models\UserDTO;
use Core\Domains\User\Services\UserDecorator;
use Core\Domains\User\UserLocator;
use Illuminate\Support\Facades\Auth;

abstract class app
{
    private static UserDTO       $user;
    private static UserDecorator $userDecorator;

    public static function user(): UserDTO
    {
        if ( ! isset(self::$user)) {
            $user = Auth::id() ? UserLocator::UserService()->getById(Auth::id()) : null;
            if ( ! $user) {
                $user = UserLocator::UserFactory()->makeUndefined();
            }
            self::$user = $user;
        }

        return self::$user;
    }

    public static function account(): AccountDTO
    {
        return self::user()->getAccount();
    }

    public static function userDecorator(): UserDecorator
    {
        if ( ! isset(self::$userDecorator)) {
            self::$userDecorator = UserLocator::UserDecorator(self::user());
        }

        return self::$userDecorator;
    }
}
