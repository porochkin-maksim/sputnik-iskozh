<?php declare(strict_types=1);

namespace Core\Domains\User\Models;

use App\Models\User;
use Core\Db\Searcher\SearcherInterface;
use Core\Db\Searcher\SearcherTrait;

class UserSearcher implements SearcherInterface
{
    use SearcherTrait;

    public function __construct()
    {
        $this
            ->setWithAccounts()
            ->setWithRoles()
            ->setWithExData()
        ;
    }

    public function setWithRoles(): static
    {
        $this->with[] = User::ROLES;

        return $this;
    }

    public function setWithExData(): static
    {
        $this->with[] = User::EX_DATA;

        return $this;
    }

    public function setWithAccounts(): static
    {
        $this->with[] = User::ACCOUNTS;

        return $this;
    }
}
