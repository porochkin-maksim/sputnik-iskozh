<?php declare(strict_types=1);

namespace Core\Domains\User;

use Core\Domains\Access\RoleLocator;
use Core\Domains\Account\AccountLocator;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Domains\User\Factories\UserFactory;
use Core\Domains\User\Models\UserDTO;
use Core\Domains\User\Repositories\UserRepository;
use Core\Domains\User\Services\UserDecorator;
use Core\Domains\User\Services\UserService;

class UserLocator
{
    private static UserFactory         $UserFactory;
    private static UserRepository      $UserRepository;
    private static UserService         $UserService;

    public static function UserService(): UserService
    {
        if ( ! isset(self::$UserService)) {
            self::$UserService = new UserService(
                self::UserFactory(),
                self::UserRepository(),
                HistoryChangesLocator::HistoryChangesService(),
            );
        }

        return self::$UserService;
    }

    public static function UserFactory(): UserFactory
    {
        if ( ! isset(self::$UserFactory)) {
            self::$UserFactory = new UserFactory(
                AccountLocator::AccountFactory(),
                RoleLocator::RoleFactory(),
            );
        }

        return self::$UserFactory;
    }

    public static function UserRepository(): UserRepository
    {
        if ( ! isset(self::$UserRepository)) {
            self::$UserRepository = new UserRepository();
        }

        return self::$UserRepository;
    }

    public static function UserDecorator(UserDTO $user): UserDecorator
    {
        return new UserDecorator($user);
    }
}
