<?php declare(strict_types=1);

namespace Core\Domains\User;

readonly class UserFactory
{
    public function makeDefault(): UserEntity
    {
        return new UserEntity();
    }

    public function makeUndefined(): UserEntity
    {
        $result = new UserEntity();
        $result
            ->setId(UserIdEnum::UNDEFINED)
            ->setLastName('Неавторизованный')
        ;

        return $result;
    }

    public function makeRobot(): UserEntity
    {
        $result = new UserEntity();
        $result
            ->setId(UserIdEnum::ROBOT)
            ->setLastName('Робот')
        ;

        return $result;
    }
}
