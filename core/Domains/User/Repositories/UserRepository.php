<?php declare(strict_types=1);

namespace Core\Domains\User\Repositories;

use App\Models\User;
use Core\Db\RepositoryTrait;
use Core\Domains\User\Collections\UserCollection;

class UserRepository
{
    private const string TABLE = User::TABLE;

    use RepositoryTrait {
        getById as traitGetById;
        getByIds as traitGetByIds;
    }

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

    public function getByIds(array $ids): UserCollection
    {
        return new UserCollection($this->traitGetByIds($ids));
    }

    public function save(User $user): User
    {
        $user->save();

        return $user;
    }
}
