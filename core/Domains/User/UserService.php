<?php declare(strict_types=1);

namespace Core\Domains\User;

use App\Models\User;
use Core\Repositories\SearcherInterface;

readonly class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    )
    {
    }

    public function search(?UserSearcher $searcher = null): UserSearchResponse
    {
        return $this->userRepository->search($searcher ?: new UserSearcher());
    }

    public function getById(int|string|null $id, bool $withDeleted = false): ?UserEntity
    {
        return $this->userRepository->getById((int) $id, $withDeleted);
    }

    public function save(UserEntity $user): UserEntity
    {
        return $this->userRepository->save($user);
    }

    public function deleteById(int $id): bool
    {
        return $this->userRepository->deleteById($id);
    }

    public function getByEmail(?string $email): ?UserEntity
    {
        return $this->search(new UserSearcher()
            ->addWhere(User::EMAIL, SearcherInterface::EQUALS, $email)
            ->setLimit(1)
        )->getItems()->first();
    }
}
