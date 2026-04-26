<?php declare(strict_types=1);

namespace Core\Domains\CounterHistory;

use App\Models\Counter\CounterHistory;
use Core\Repositories\SearcherInterface;

readonly class CounterHistoryService
{
    public function __construct(
        private CounterHistoryRepositoryInterface $counterHistoryRepository,
    )
    {
    }

    public function search(CounterHistorySearcher $searcher): CounterHistorySearchResponse
    {
        return $this->counterHistoryRepository->search($searcher);
    }

    public function getById(?int $id): ?CounterHistoryEntity
    {
        return $this->counterHistoryRepository->getById($id);
    }

    public function save(CounterHistoryEntity $counter): CounterHistoryEntity
    {
        return $this->counterHistoryRepository->save($counter);
    }

    public function deleteById(?int $id): bool
    {
        return $this->counterHistoryRepository->deleteById($id);
    }

    public function getPrevious(CounterHistoryEntity $counterHistory): ?CounterHistoryEntity
    {
        $histories = $this->search((new CounterHistorySearcher())
            ->setCounterId($counterHistory->getCounterId())
            ->setSortOrderProperty(CounterHistory::DATE, SearcherInterface::SORT_ORDER_ASC))->getItems();

        $result = null;
        foreach ($histories as $item) {
            if ($item->getDate()?->gte($counterHistory->getDate())) {
                break;
            }
            $result = $item;
        }
        return $result;
    }

    public function getLastByCounterId(?int $counterId): ?CounterHistoryEntity
    {
        return $this->search((new CounterHistorySearcher())
            ->setCounterId($counterId)
            ->setLimit(1)
            ->setWithClaim()
            ->setSortOrderProperty(CounterHistory::DATE, SearcherInterface::SORT_ORDER_DESC))->getItems()->first();
    }
}
