<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Roles;

use App\Http\Resources\AbstractResource;
use Core\Domains\Access\RoleCollection;

readonly class RolesListResource extends AbstractResource
{
    public function __construct(
        private RoleCollection $roleCollection,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $result = [];
        foreach ($this->roleCollection as $role) {
            $result[] = new RoleResource($role);
        }

        return $result;
    }
}
