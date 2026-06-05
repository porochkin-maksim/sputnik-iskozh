<?php declare(strict_types=1);

namespace Core\App\Access;

use Core\Domains\Access\RoleEntity;
use Core\Domains\Access\RoleFactory;
use Core\Domains\Access\RoleService;
use Core\Exceptions\ValidationException;

readonly class SaveRoleCommand
{
    public function __construct(
        private RoleFactory       $roleFactory,
        private RoleService       $roleService,
        private SaveRoleValidator $validator,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function execute(?int $id, ?string $name, array $permissions): ?RoleEntity
    {
        $this->validator->validate($name, $permissions);

        $role = $id
            ? $this->roleService->getById($id)
            : $this->roleFactory->makeDefault();

        if ($role === null) {
            return null;
        }

        $role
            ->setName($name)
            ->setPermissions($permissions)
        ;

        return $this->roleService->save($role);
    }
}
