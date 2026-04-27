<?php declare(strict_types=1);

namespace Core\Domains\Account;

use Core\Domains\Account\Factories\AccountFactory;
use Core\Domains\Account\Repositories\AccountRepository;
use Core\Domains\Account\Repositories\AccountToUserRepository;
use Core\Domains\Account\Services\AccountService;

class AccountLocator
{
    private static AccountService          $AccountService;
    private static AccountFactory          $AccountFactory;
    private static AccountRepository       $AccountRepository;
    private static AccountToUserRepository $AccountToUserRepository;

    public static function AccountService(): AccountService
    {
        if ( ! isset(self::$AccountService)) {
            self::$AccountService = new AccountService(
                self::AccountRepository(),
            );
        }

        return self::$AccountService;
    }

    public static function AccountFactory(): AccountFactory
    {
        if ( ! isset(self::$AccountFactory)) {
            self::$AccountFactory = new AccountFactory();
        }

        return self::$AccountFactory;
    }

    public static function AccountRepository(): AccountRepository
    {
        if ( ! isset(self::$AccountRepository)) {
            self::$AccountRepository = new AccountRepository(
                self::AccountFactory(),
                self::AccountToUserRepository(),
            );
        }

        return self::$AccountRepository;
    }

    public static function AccountToUserRepository(): AccountToUserRepository
    {
        if ( ! isset(self::$AccountToUserRepository)) {
            self::$AccountToUserRepository = new AccountToUserRepository();
        }

        return self::$AccountToUserRepository;
    }
}
