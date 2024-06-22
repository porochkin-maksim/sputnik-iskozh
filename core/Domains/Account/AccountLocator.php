<?php declare(strict_types=1);

namespace Core\Domains\Account;

use Core\Domains\Account\Factories\AccountFactory;
use Core\Domains\Account\Repositories\AccountRepository;
use Core\Domains\Account\Repositories\AccountToUserRepository;
use Core\Domains\Account\Services\AccountService;
use Core\Domains\Option\OptionLocator;

class AccountLocator
{
    private static AccountService          $accountService;
    private static AccountFactory          $accountFactory;
    private static AccountRepository       $accountRepository;
    private static AccountToUserRepository $accountToUserRepository;

    public static function AccountService(): AccountService
    {
        if ( ! isset(self::$accountService)) {
            self::$accountService = new AccountService(
                self::AccountFactory(),
                self::AccountRepository(),
                OptionLocator::OptionService(),
            );
        }

        return self::$accountService;
    }

    public static function AccountFactory(): AccountFactory
    {
        if ( ! isset(self::$accountFactory)) {
            self::$accountFactory = new AccountFactory();
        }

        return self::$accountFactory;
    }

    public static function AccountRepository(): AccountRepository
    {
        if ( ! isset(self::$accountRepository)) {
            self::$accountRepository = new AccountRepository(
                self::AccountToUserRepository(),
            );
        }

        return self::$accountRepository;
    }

    public static function AccountToUserRepository(): AccountToUserRepository
    {
        if ( ! isset(self::$accountToUserRepository)) {
            self::$accountToUserRepository = new AccountToUserRepository();
        }

        return self::$accountToUserRepository;
    }
}
