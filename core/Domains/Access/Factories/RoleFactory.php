<?php declare(strict_types=1);

namespace Core\Domains\Access\Factories;

use App\Models\Access\Role;
use Core\Domains\Access\Enums\RoleIdEnum;
use Core\Domains\Access\Enums\UserToRole;
use Core\Domains\Access\Models\RoleDTO;
use Core\Domains\User\UserLocator;

readonly class RoleFactory
{
    public function make(RoleIdEnum $roleIdEnum): RoleDTO
    {
        $result = new RoleDTO();
        $result->setId($roleIdEnum->value);

        return $result;
    }

    public function makeForUserId(?int $userId): ?RoleDTO
    {
        $roleEnum = UserToRole::getForUser($userId);
        return $roleEnum ? $this->make($roleEnum) : null;
    }

    public function makeModelFromDto(RoleDTO $dto, ?Role $role = null): Role
    {
        if ($role) {
            $result = $role;
        }
        else {
            $result = Role::make();
        }
        $role->fill([Role::ID => $dto->getId(),]);

        return $result;
    }

    public function makeDtoFromObject(Role $role): RoleDTO
    {
        $result = new RoleDTO();

        $result->setId($role->id);

        foreach ($role->users ?? [] as $user) {
            $result->addUser(UserLocator::UserFactory()->makeDtoFromObject($user));
        }

        return $result;
    }
}