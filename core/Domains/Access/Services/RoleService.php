<?php declare(strict_types=1);

namespace Core\Domains\Access\Services;

use Core\Domains\Access\Collections\RoleCollection;
use Core\Domains\Access\Factories\RoleFactory;
use Core\Domains\Access\Models\RoleComparator;
use Core\Domains\Access\Models\RoleDTO;
use Core\Domains\Access\Models\RoleSearcher;
use Core\Domains\Access\Repositories\RoleRepository;
use Core\Domains\Access\Responses\SearchResponse;
use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\Services\HistoryChangesService;

readonly class RoleService
{
    public function __construct(
        private RoleFactory           $roleFactory,
        private RoleRepository        $roleRepository,
        private HistoryChangesService $historyChangesService,
    )
    {
    }

    public function getByUserId(int|string|null $id): ?RoleDTO
    {
        $result = $this->roleFactory->makeForUserId($id);

        if ($result) {
            return $result;
        }

        $result = $this->roleRepository->getByUserId((int) $id);

        return $result ? $this->roleFactory->makeDtoFromObject($result) : null;
    }

    public function save(RoleDTO $role)
    {
        $model = $this->roleRepository->getById($role->getId());
        if ($model) {
            $before = $this->roleFactory->makeDtoFromObject($model);
        }
        else {
            $before = new RoleDTO();
        }

        $model   = $this->roleRepository->save($this->roleFactory->makeModelFromDto($role, $model));
        $current = $this->roleFactory->makeDtoFromObject($model);

        $this->historyChangesService->writeToHistory(
            $role->getId() ? Event::UPDATE : Event::CREATE,
            HistoryType::ROLE,
            $current->getId(),
            null,
            null,
            new RoleComparator($current),
            new RoleComparator($before),
        );

        return $current;
    }

    public function search(RoleSearcher $searcher): SearchResponse
    {
        $response = $this->roleRepository->search($searcher);

        $result = new SearchResponse();
        $result->setTotal($response->getTotal());

        $collection = new RoleCollection();
        foreach ($response->getItems() as $item) {
            $collection->add($this->roleFactory->makeDtoFromObject($item));
        }

        return $result->setItems($collection);
    }

    public function getById(int $id): ?RoleDTO
    {
        $result = $this->roleRepository->getById($id);

        return $result ? $this->roleFactory->makeDtoFromObject($result) : null;
    }

    public function deleteById(int $id): bool
    {
        $user = $this->getById($id);

        if ( ! $user) {
            return false;
        }

        $this->historyChangesService->writeToHistory(
            Event::DELETE,
            HistoryType::ROLE,
            $user->getId(),
        );

        return $this->roleRepository->deleteById($id);
    }
}
