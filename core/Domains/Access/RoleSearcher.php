<?php declare(strict_types=1);

namespace Core\Domains\Access;

use App\Models\Access\Role;
use Core\Repositories\BaseSearcher;

class RoleSearcher extends BaseSearcher
{
    public function setWithUsers(): static
    {
        $this->with[] = Role::USERS;

        return $this;
    }
}
