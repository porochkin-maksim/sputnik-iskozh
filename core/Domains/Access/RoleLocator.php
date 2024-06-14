<?php declare(strict_types=1);

namespace Core\Domains\Access;

use Core\Domains\Access\Factories\RoleFactory;
use Core\Domains\Access\Repositories\RoleRepository;
use Core\Domains\Access\Repositories\RoleToUserRepository;
use Core\Domains\Access\Services\RoleService;

class RoleLocator
{
    private static RoleService          $roleService;
    private static RoleFactory          $roleFactory;
    private static RoleRepository       $roleRepository;
    private static RoleToUserRepository $roleToUserRepository;

    public static function RoleService(): RoleService
    {
        if ( ! isset(self::$roleService)) {
            self::$roleService = new RoleService(
                self::RoleFactory(),
                self::RoleRepository(),
            );
        }

        return self::$roleService;
    }

    public static function RoleFactory(): RoleFactory
    {
        if ( ! isset(self::$roleFactory)) {
            self::$roleFactory = new RoleFactory();
        }

        return self::$roleFactory;
    }

    public static function RoleRepository(): RoleRepository
    {
        if ( ! isset(self::$roleRepository)) {
            self::$roleRepository = new RoleRepository(
                self::RoleToUserRepository(),
            );
        }

        return self::$roleRepository;
    }

    public static function RoleToUserRepository(): RoleToUserRepository
    {
        if ( ! isset(self::$roleToUserRepository)) {
            self::$roleToUserRepository = new RoleToUserRepository();
        }

        return self::$roleToUserRepository;
    }
}
