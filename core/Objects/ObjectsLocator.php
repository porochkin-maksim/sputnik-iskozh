<?php declare(strict_types=1);

namespace Core\Objects;

use Core\Objects\User\UserLocator;

abstract class ObjectsLocator
{
    private static UserLocator $UserLocator;

    public static function Users(): UserLocator
    {
        if (!isset(self::$UserLocator)) {
            self::$UserLocator = new UserLocator();
        }

        return self::$UserLocator;
    }
}
