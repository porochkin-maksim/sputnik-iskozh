<?php declare(strict_types=1);

namespace Core\Objects\Account;

use Core\Objects\Account\Factories\AccountFactory;
use Core\Objects\Account\Repositories\AccountRepository;
use Core\Objects\Account\Repositories\AccountToUserRepository;
use Core\Objects\Account\Services\AccountService;
use Core\Objects\User\UserLocator;

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
            );
        }

        return self::$accountService;
    }

    public static function AccountFactory(): AccountFactory
    {
        if ( ! isset(self::$accountFactory)) {
            self::$accountFactory = new AccountFactory(
                UserLocator::UserFactory(),
            );
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
