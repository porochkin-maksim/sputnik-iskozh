<?php declare(strict_types=1);

namespace Core\Domains\Access;

class RoleFactory
{
    public function makeDefault(): RoleEntity
    {
        return new RoleEntity();
    }

    public function make(RoleEnum $roleEnum): RoleEntity
    {
        return $this->makeDefault()
            ->setId($roleEnum->value)
            ->setName($roleEnum->name())
            ->setPermissions(PermissionEnum::values());
    }

    public function makeForUserId(?int $userId): ?RoleEntity
    {
        $roleEnum = UserToRole::getForUser($userId);

        return $roleEnum ? $this->make($roleEnum) : null;
    }
}
