<?php declare(strict_types=1);

namespace Core\Domains\Counter\Services;

use App\Models\Counter\CounterHistory;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Counter\Models\CounterHistoryDTO;
use Core\Domains\Counter\Models\CounterHistorySearcher;
use Core\Domains\Counter\Repositories\CounterHistoryRepository;
use Core\Domains\Counter\Responses\CounterHistorySearchResponse;

readonly class CounterHistoryService
{
    public function __construct(
        private CounterHistoryRepository $counterHistoryRepository,
    )
    {
    }

    public function search(CounterHistorySearcher $searcher): CounterHistorySearchResponse
    {
        return $this->counterHistoryRepository->search($searcher);
    }

    public function getById(?int $id): ?CounterHistoryDTO
    {
        return $this->counterHistoryRepository->getById($id);
    }

    public function save(CounterHistoryDTO $counter): CounterHistoryDTO
    {
        return $this->counterHistoryRepository->save($counter);
    }

    public function deleteById(?int $id): bool
    {
        return $this->counterHistoryRepository->deleteById($id);
    }

    public function getPrevious(CounterHistoryDTO $counterHistory): ?CounterHistoryDTO
    {
        $histories = $this->search(new CounterHistorySearcher()
            ->setCounterId($counterHistory->getCounterId())
            ->setSortOrderProperty(CounterHistory::DATE, SearcherInterface::SORT_ORDER_ASC),
        )->getItems();

        $result = null;
        foreach ($histories as $item) {
            if ($item->getDate()?->gte($counterHistory->getDate())) {
                break;
            }

            $result = $item;
        }

        return $result;
    }

    public function getLastByCounterId(?int $counterId): ?CounterHistoryDTO
    {
        return $this->search(new CounterHistorySearcher()
            ->setCounterId($counterId)
            ->setLimit(1)
            ->setWithClaim()
            ->setSortOrderProperty(CounterHistory::DATE, SearcherInterface::SORT_ORDER_DESC),
        )->getItems()->first();
    }
}
