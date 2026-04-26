<?php declare(strict_types=1);

use App\Locators\CounterLocator;
use App\Session\SessionNames;
use Core\Domains\Access\RoleDecorator;
use Core\Domains\Access\RoleEntity;
use Core\Domains\Access\RoleService;
use Core\Domains\Account\AccountEntity;
use Core\Domains\Account\AccountFactory;
use Core\Domains\Account\AccountService;
use Core\Domains\Counter\CounterCollection;
use Core\Domains\User\UserEntity;
use Core\Domains\User\UserFactory;
use Core\Domains\User\UserService;
use Core\Domains\User\UserViewer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

abstract class lc
{
    private static UserEntity        $user;
    private static RoleEntity        $role;
    private static CounterCollection $counters;
    private static AccountEntity     $account;
    private static RoleDecorator $roleDecorator;
    private static UserViewer    $userDecorator;

    public static function isAuth(): bool
    {
        return (bool) Auth::id();
    }

    public static function user(): UserEntity
    {
        if ( ! isset(self::$user)) {
            $user = Auth::id() ? app(UserService::class)->getById(Auth::id()) : null;
            if ( ! $user) {
                if (self::isCli()) {
                    $user = app(UserFactory::class)->makeRobot();
                }
                else {
                    $user = app(UserFactory::class)->makeUndefined();
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

    public static function account(): AccountEntity
    {
        if ( ! isset(self::$account)) {
            $accountId = Session::get(SessionNames::ACCOUNT_ID);
            $accountService = app(AccountService::class);
            $accountFactory = app(AccountFactory::class);
            $accounts  = Auth::id() ? $accountService->getByUserId(Auth::id()) : null;
            if ($accountId && $accounts !== null && in_array($accountId, $accounts->getIds())) {
                $account = $accountService->getById($accountId);
            }
            else {
                $account = $accounts?->first();
            }
            if ( ! $account) {
                $account = $accountFactory->makeDefault();
            }

            $account->setFraction($account->getUsers()->getById(Auth::id())?->getFraction());

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

    public static function role(): RoleEntity
    {
        if ( ! isset(self::$role)) {
            $role = app(RoleService::class)->getByUserId(Auth::id());
            if ( ! $role) {
                $role = new RoleEntity();
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

    public static function userDecorator(): UserViewer
    {
        if ( ! isset(self::$userDecorator)) {
            self::$userDecorator = new UserViewer(self::user());
        }

        return self::$userDecorator;
    }

    public static function isSuperAdmin(): bool
    {
        return self::RoleDecorator()->isSuperAdmin();
    }

    public static function isAndroid(): bool
    {
        if ( ! Session::has(SessionNames::IS_ANDROID)) {
            Session::put(SessionNames::IS_ANDROID, request()->header('X-Client-Type') === 'android-app');
        }

        return Session::get(SessionNames::IS_ANDROID);
    }
}
