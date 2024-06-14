<?php declare(strict_types=1);

namespace Core\Objects\User\Services;

use Core\Objects\User\Collections\Users;
use Core\Objects\User\Factories\UserFactory;
use Core\Objects\User\Models\UserDTO;
use Core\Objects\User\Models\UserSearcher;
use Core\Objects\User\Repositories\UserRepository;

readonly class UserService
{
    public function __construct(
        private UserFactory    $userFactory,
        private UserRepository $userRepository,
    )
    {
    }

    public function getById(?int $id): ?UserDTO
    {
        $result = $this->userRepository->getById($id);

        return $result ? $this->userFactory->makeDtoFromObject($result) : null;
    }

    public function getByIds(array $ids): Users
    {
        return $this->userRepository->getByIds($ids);
    }

    public function save(UserDTO $dto): UserDTO
    {
        $user = $dto->getModel();

        if ( ! $user) {
            $user = $this->userRepository->getById($dto->getId());
        }

        $user = $this->userFactory->makeModelFromDto($dto, $user);
        $user = $this->userRepository->save($user);

        return $this->userFactory->makeDtoFromObject($user);
    }

    public function search(UserSearcher $searcher): Users
    {
        $users = $this->userRepository->search($searcher);

        $result  = new Users();
        foreach ($users->getItems() as $user) {
            $result->add($this->userFactory->makeDtoFromObject($user));
        }

        return $result;
    }

    public function searchById(int|string|null $id)
    {
        $userSearcher = new UserSearcher();
        $userSearcher
            ->setId($id)
            ->setWithAccounts()
            ->setWithRoles();

        return $this->search($userSearcher)->first();
    }
}
