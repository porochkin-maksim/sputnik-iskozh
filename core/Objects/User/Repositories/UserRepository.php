<?php declare(strict_types=1);

namespace Core\Objects\User\Repositories;

use App\Models\User;
use Core\Cache\CacheRepositoryInterface;
use Core\Db\RepositoryTrait;
use Core\Db\UseCacheRepositoryInterface;
use Core\Objects\User\Collections\Users;

class UserRepository
{
    use RepositoryTrait {
        getById as traitGetById;
        getByIds as traitGetByIds;
    }

    public function __construct(
        private readonly CacheRepositoryInterface $userCacheRepository,
    ) {}

    protected function modelClass(): string
    {
        return User::class;
    }

    public function getById(?int $id, bool $cache = false): ?User
    {
        /** @var ?User $result */
        $result = $this->traitGetById($id, $cache);

        return $result;
    }

    public function getByIds(array $ids, bool $cache = false): Users
    {
        return new Users($this->traitGetByIds($ids, $cache));
    }

    public function save(User $user): User
    {
        $user->save();

        return $user;
    }
}
