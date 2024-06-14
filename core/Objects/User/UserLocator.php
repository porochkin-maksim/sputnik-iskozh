<?php declare(strict_types=1);

namespace Core\Objects\User;

use Core\Objects\Access\RoleLocator;
use Core\Objects\Account\AccountLocator;
use Core\Objects\User\Factories\UserFactory;
use Core\Objects\User\Models\UserDTO;
use Core\Objects\User\Repositories\UserCacheRepository;
use Core\Objects\User\Repositories\UserRepository;
use Core\Objects\User\Services\UserDecorator;
use Core\Objects\User\Services\UserService;

class UserLocator
{
    private static UserCacheRepository $UserCacheRepository;
    private static UserFactory         $UserFactory;
    private static UserRepository      $UserRepository;
    private static UserService         $UserService;
    private static UserDecorator       $UserDecorator;

    public static function UserService(): UserService
    {
        if (!isset(self::$UserService)) {
            self::$UserService = new UserService(
                self::UserFactory(),
                self::UserRepository(),
            );
        }

        return self::$UserService;
    }

    public static function UserFactory(): UserFactory
    {
        if (!isset(self::$UserFactory)) {
            self::$UserFactory = new UserFactory(
                AccountLocator::AccountFactory(),
                RoleLocator::RoleFactory(),
            );
        }

        return self::$UserFactory;
    }

    public static function UserRepository(): UserRepository
    {
        if (!isset(self::$UserRepository)) {
            self::$UserRepository = new UserRepository(
                self::UserCacheRepository(),
            );
        }

        return self::$UserRepository;
    }

    public static function UserCacheRepository(): UserCacheRepository
    {
        if (!isset(self::$UserCacheRepository)) {
            self::$UserCacheRepository = new UserCacheRepository();
        }

        return self::$UserCacheRepository;
    }

    public static function UserDecorator(?UserDTO $user): UserDecorator
    {
        if (!isset(self::$UserDecorator)) {
            self::$UserDecorator = new UserDecorator($user);
        }

        return self::$UserDecorator;
    }
}
