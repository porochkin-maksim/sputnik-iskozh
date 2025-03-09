<?php declare(strict_types=1);

namespace Core\Domains\User\Services;

use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\Services\HistoryChangesService;
use Core\Domains\User\Collections\UserCollection;
use Core\Domains\User\Factories\UserFactory;
use Core\Domains\User\Models\UserComparator;
use Core\Domains\User\Models\UserDTO;
use Core\Domains\User\Models\UserSearcher;
use Core\Domains\User\Repositories\UserRepository;
use Core\Domains\User\Responses\SearchResponse;

readonly class UserService
{
    public function __construct(
        private UserFactory           $userFactory,
        private UserRepository        $userRepository,
        private HistoryChangesService $historyChangesService,
    )
    {
    }

    public function save(UserDTO $user): UserDTO
    {
        $model = $this->userRepository->getById($user->getId());
        if ($model) {
            $before = $this->userFactory->makeDtoFromObject($model);
        }
        else {
            $before = new UserDTO();
        }

        $model   = $this->userRepository->save($this->userFactory->makeModelFromDto($user, $model));
        $current = $this->userFactory->makeDtoFromObject($model);

        $this->historyChangesService->writeToHistory(
            $user->getId() ? Event::UPDATE : Event::CREATE,
            HistoryType::INVOICE,
            $current->getId(),
            null,
            null,
            new UserComparator($current),
            new UserComparator($before),
        );

        return $current;
    }

    public function search(UserSearcher $searcher): SearchResponse
    {
        $response = $this->userRepository->search($searcher);

        $result = new SearchResponse();
        $result->setTotal($response->getTotal());

        $collection = new UserCollection();
        foreach ($response->getItems() as $item) {
            $collection->add($this->userFactory->makeDtoFromObject($item));
        }

        return $result->setItems($collection);
    }

    public function getById(?int $id): ?UserDTO
    {
        if ( ! $id) {
            return null;
        }

        $searcher = new UserSearcher();
        $searcher
            ->setId($id)
            ->setWithAccounts()
            ->setWithRoles()
        ;
        $result = $this->userRepository->search($searcher)->getItems()->first();

        return $result ? $this->userFactory->makeDtoFromObject($result) : null;
    }

    public function deleteById(int $id): bool
    {
        $user = $this->getById($id);

        if ( ! $user) {
            return false;
        }

        $this->historyChangesService->writeToHistory(
            Event::DELETE,
            HistoryType::USER,
            $user->getId(),
        );

        return $this->userRepository->deleteById($id);
    }
}
