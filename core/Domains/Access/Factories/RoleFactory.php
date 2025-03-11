<?php declare(strict_types=1);

namespace Core\Domains\Access\Factories;

use App\Models\Access\Role;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Access\Enums\RoleIdEnum;
use Core\Domains\Access\Enums\UserToRole;
use Core\Domains\Access\Models\RoleDTO;
use Core\Domains\User\UserLocator;

readonly class RoleFactory
{
    public function makeDefault(): RoleDTO
    {
        return new RoleDTO();
    }

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

    public function makeModelFromDto(RoleDTO $dto, ?Role $model = null): Role
    {
        if ($model) {
            $result = $model;
        }
        else {
            $result = Role::make();
        }

        $result->permissions = array_map(static fn(PermissionEnum $permission) => $permission->value, $dto->getPermissions());

        return $result->fill([
            Role::NAME => $dto->getName(),
        ]);
    }

    public function makeDtoFromObject(Role $model): RoleDTO
    {
        $result = new RoleDTO();

        $result
            ->setId($model->id)
            ->setName($model->name)
            ->setCreatedAt($model->created_at)
            ->setUpdatedAt($model->updated_at)
            ->setPermissions($model->permissions)
        ;

        if (isset($model->getRelations()[Role::USERS])) {
            foreach ($model->getRelation(Role::USERS) as $user) {
                $result->addUser(UserLocator::UserFactory()->makeDtoFromObject($user));
            }
        }

        return $result;
    }
}