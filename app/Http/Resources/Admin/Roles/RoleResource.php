<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Roles;

use lc;
use App\Http\Resources\AbstractResource;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Access\Models\RoleDTO;
use Core\Responses\ResponsesEnum;

readonly class RoleResource extends AbstractResource
{
    public function __construct(
        private RoleDTO $role,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $access = lc::roleDecorator();

        return [
            'id'          => $this->role->getId(),
            'name'        => $this->role->getName(),
            'permissions' => array_values(array_map(static fn(PermissionEnum $permission) => (string) ($permission->value), $this->role->getPermissions())),
            'actions'     => [
                ResponsesEnum::VIEW => $access->can(PermissionEnum::ROLES_VIEW),
                ResponsesEnum::EDIT => $access->can(PermissionEnum::ROLES_EDIT),
                ResponsesEnum::DROP => $access->can(PermissionEnum::ROLES_DROP),
            ],
        ];
    }
}