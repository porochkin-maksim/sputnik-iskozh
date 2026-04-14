<?php declare(strict_types=1);

namespace Core\Domains\User\Services;

use App\Models\User;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\User\Models\UserDTO;
use Core\Domains\User\Models\UserSearcher;
use Core\Domains\User\Repositories\UserRepository;
use Core\Domains\User\Responses\UserSearchResponse;

readonly class UserService
{
    public function __construct(
        private UserRepository $userRepository,
    )
    {
    }

    public function search(?UserSearcher $searcher = null): UserSearchResponse
    {
        return $this->userRepository->search($searcher ?: new UserSearcher());
    }

    public function getById(int|string|null $id, bool $withDeleted = false): ?UserDTO
    {
        return $this->userRepository->getById($id, $withDeleted);
    }

    public function save(UserDTO $user): UserDTO
    {
        return $this->userRepository->save($user);
    }

    public function deleteById(int $id): bool
    {
        return $this->userRepository->deleteById($id);
    }

    public function getByEmail(?string $email): ?UserDTO
    {
        return $this->search(new UserSearcher()
            ->addWhere(User::EMAIL, SearcherInterface::EQUALS, $email)
            ->setLimit(1),
        )->getItems()->first();
    }
}
