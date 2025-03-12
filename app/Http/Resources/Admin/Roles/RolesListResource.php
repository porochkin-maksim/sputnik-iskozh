<?php

namespace App\Http\Resources\Admin\Roles;

use app;
use App\Http\Resources\AbstractResource;
use Core\Domains\Access\Collections\RoleCollection;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Responses\ResponsesEnum;

readonly class RolesListResource extends AbstractResource
{
    public function __construct(
        private RoleCollection $roleCollection,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $access = app::roleDecorator();
        $result = [
            'roles'      => [],
            'actions'    => [
                ResponsesEnum::CREATE => $access->can(PermissionEnum::ROLES_CREATE),
                ResponsesEnum::EDIT   => $access->can(PermissionEnum::ROLES_EDIT),
                ResponsesEnum::VIEW   => $access->can(PermissionEnum::ROLES_VIEW),
                ResponsesEnum::DROP   => $access->can(PermissionEnum::ROLES_DROP),
            ],
            'historyUrl' => HistoryChangesLocator::route(
                type: HistoryType::ROLE,
            ),
        ];

        foreach ($this->roleCollection as $role) {
            $result['roles'][] = new RoleResource($role);
        }

        return $result;
    }
}