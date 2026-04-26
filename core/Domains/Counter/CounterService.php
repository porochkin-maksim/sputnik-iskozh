<?php declare(strict_types=1);

namespace Core\Domains\Counter;

use App\Models\Counter\Counter;
use Core\Repositories\SearcherInterface;

readonly class CounterService
{
    public function __construct(
        private CounterRepositoryInterface $counterRepository,
    )
    {
    }

    public function search(CounterSearcher $searcher): CounterSearchResponse
    {
        return $this->counterRepository->search($searcher);
    }

    public function getById(?int $id): ?CounterEntity
    {
        return $this->counterRepository->getById($id);
    }

    public function save(CounterEntity $counter): CounterEntity
    {
        return $this->counterRepository->save($counter);
    }

    public function deleteById(?int $id): bool
    {
        return $this->counterRepository->deleteById($id);
    }

    public function getByAccountId(?int $accountId): CounterCollection
    {
        if ( ! $accountId) {
            return new CounterCollection();
        }

        $searcher = new CounterSearcher();
        $searcher
            ->setAccountId($accountId)
            ->setSortOrderProperty(Counter::IS_INVOICING, SearcherInterface::SORT_ORDER_DESC)
            ->setSortOrderProperty(Counter::ID, SearcherInterface::SORT_ORDER_DESC);

        return $this->counterRepository->search($searcher)->getItems();
    }
}
