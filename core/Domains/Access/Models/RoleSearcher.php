<?php declare(strict_types=1);

namespace Core\Domains\Access\Models;

use App\Models\Access\Role;
use Core\Db\Searcher\SearcherInterface;
use Core\Db\Searcher\SearcherTrait;

class RoleSearcher implements SearcherInterface
{
    use SearcherTrait;

    public function setWithUsers(): static
    {
        $this->with[] = Role::USERS;

        return $this;
    }
}
