<?php declare(strict_types=1);

namespace Core\Objects\User;

use App\Models\User;
use Core\Objects\User\Factories\UserFactory;
use Core\Objects\User\Repositories\UserCacheRepository;
use Core\Objects\User\Repositories\UserRepository;
use Core\Objects\User\Repositories\UserRepositoryInterface;
use Core\Objects\User\Services\UserDecorator;
use Core\Objects\User\Services\UserService;
use Core\Objects\User\Services\UserServiceInterface;

class UserLocator
{
    private static UserCacheRepository $UserCacheRepository;
    private static UserFactory         $UserFactory;
    private static UserRepository      $UserRepository;
    private static UserService         $UserService;
    private static UserDecorator       $UserDecorator;

    public static function UserService(): UserServiceInterface
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
            self::$UserFactory = new UserFactory();
        }

        return self::$UserFactory;
    }

    public static function UserRepository(): UserRepositoryInterface
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

    public static function UserDecorator(?User $user): UserDecorator
    {
        if (!isset(self::$UserDecorator)) {
            self::$UserDecorator = new UserDecorator($user);
        }

        return self::$UserDecorator;
    }
}
