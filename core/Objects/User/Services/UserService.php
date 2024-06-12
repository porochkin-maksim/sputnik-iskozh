<?php declare(strict_types=1);

namespace Core\Objects\User\Services;

use App\Models\User;
use Core\Objects\User\Collections\Users;
use Core\Objects\User\Factories\UserFactory;
use Core\Objects\User\Models\UserDTO;
use Core\Objects\User\Repositories\UserRepository;

class UserService implements UserServiceInterface
{
    public function __construct(
        private readonly UserFactory    $userFactory,
        private readonly UserRepository $userRepository,
    ) {}

    public function getById(?int $id): ?User
    {
        return $this->userRepository->getById($id);
    }

    public function getByIds(array $ids, bool $cache = false): Users
    {
        return $this->userRepository->getByIds($ids);
    }

    public function save(UserDTO $dto): User
    {
        $user = $this->userFactory->makeModelFromDto($dto);
        $user = $this->userRepository->save($user);

        return $user;
    }
}
