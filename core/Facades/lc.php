<?php declare(strict_types=1);

use Core\Domains\Access\Models\RoleDTO;
use Core\Domains\Access\RoleLocator;
use Core\Domains\Access\Services\RoleDecorator;
use Core\Domains\Account\AccountLocator;
use Core\Domains\Account\Models\AccountDTO;
use Core\Domains\Counter\Collections\CounterCollection;
use Core\Domains\Counter\CounterLocator;
use Core\Domains\User\Models\UserDTO;
use Core\Domains\User\Services\UserDecorator;
use Core\Domains\User\UserLocator;
use Illuminate\Support\Facades\Auth;

abstract class lc
{
    private static UserDTO           $user;
    private static RoleDTO           $role;
    private static CounterCollection $counters;
    private static AccountDTO        $account;
    private static RoleDecorator     $roleDecorator;
    private static UserDecorator     $userDecorator;

    public static function isAuth(): bool
    {
        return (bool) Auth::id();
    }

    public static function user(): UserDTO
    {
        if ( ! isset(self::$user)) {
            $user = Auth::id() ? UserLocator::UserService()->getById(Auth::id()) : null;
            if ( ! $user) {
                if (self::isCli()) {
                    $user = UserLocator::UserFactory()->makeRobot();
                }
                else {
                    $user = UserLocator::UserFactory()->makeUndefined();
                }
            }
            $user->setRole(self::role());
            $user->setAccount(self::account());

            self::$user = $user;
        }

        return self::$user;
    }

    public static function isCli(): bool
    {
        return App::runningInConsole();
    }

    public static function account(): AccountDTO
    {
        if ( ! isset(self::$account)) {
            $account = Auth::id() ? AccountLocator::AccountService()->getByUserId(Auth::id()) : null;
            if ( ! $account) {
                $account = AccountLocator::AccountFactory()->makeDefault();
            }

            self::$account = $account;
        }

        return self::$account;
    }

    public static function counters(): CounterCollection
    {
        if ( ! isset(self::$counters)) {
            $counters = new CounterCollection();
            if (self::account()->getId() !== null) {
                $counters = CounterLocator::CounterService()->getByAccountId(self::account()->getId());
            }

            self::$counters = $counters;
        }

        return self::$counters;
    }

    public static function role(): RoleDTO
    {
        if ( ! isset(self::$role)) {
            $role = RoleLocator::RoleService()->getByUserId(Auth::id());
            if ( ! $role) {
                $role = new RoleDTO();
            }
            self::$role = $role;
        }

        return self::$role;
    }

    public static function roleDecorator(): RoleDecorator
    {
        if ( ! isset(self::$roleDecorator)) {
            self::$roleDecorator = new RoleDecorator(self::role());
        }

        return self::$roleDecorator;
    }

    public static function userDecorator(): UserDecorator
    {
        if ( ! isset(self::$userDecorator)) {
            self::$userDecorator = UserLocator::UserDecorator(self::user());
        }

        return self::$userDecorator;
    }

    public static function isSuperAdmin(): bool
    {
        return self::RoleDecorator()->isSuperAdmin();
    }
}
