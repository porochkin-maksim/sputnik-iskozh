<?php

namespace App\Http\Resources\Admin\Roles;

use App\Http\Resources\AbstractResource;
use App\Http\Resources\Common\SelectOptionResource;
use Core\Domains\Access\Collections\RoleCollection;

readonly class RolesSelectResource extends AbstractResource
{
    public function __construct(
        private RoleCollection $roleCollection,
        private bool           $addEmptyOption,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $result = [];

        if ($this->addEmptyOption) {
            $result[] = new SelectOptionResource(0, 'Без роли');
        }
        foreach ($this->roleCollection as $role) {
            $result[] = new SelectOptionResource($role->getId(), $role->getName());
        }

        return $result;
    }
}