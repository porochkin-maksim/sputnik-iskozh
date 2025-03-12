<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Roles;

use App\Http\Resources\AbstractResource;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Access\Models\RoleDTO;

readonly class RoleResource extends AbstractResource
{
    public function __construct(
        private RoleDTO $role,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $access = \app::roleDecorator();
        return [
            'id'          => $this->role->getId(),
            'name'        => $this->role->getName(),
            'permissions' => array_values(array_map(static fn(PermissionEnum $permission) => (string) ($permission->value), $this->role->getPermissions())),
            'actions'     => [
                'edit' => $access->can(PermissionEnum::ROLES_EDIT),
                'drop' => $access->can(PermissionEnum::ROLES_DROP),
            ],
        ];
    }
}