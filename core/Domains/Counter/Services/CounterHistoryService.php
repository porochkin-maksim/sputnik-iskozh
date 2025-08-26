<?php declare(strict_types=1);

namespace Core\Domains\Counter\Services;

use App\Models\Counter\CounterHistory;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Counter\Collections\CounterHistoryCollection;
use Core\Domains\Counter\Events\CounterHistoryCreatedEvent;
use Core\Domains\Counter\Events\CounterHistoryDeletingEvent;
use Core\Domains\Counter\Events\CounterHistoryUpdatedEvent;
use Core\Domains\Counter\Factories\CounterHistoryFactory;
use Core\Domains\Counter\Models\CounterHistoryComparator;
use Core\Domains\Counter\Models\CounterHistoryDTO;
use Core\Domains\Counter\Models\CounterHistorySearcher;
use Core\Domains\Counter\Repositories\CounterHistoryRepository;
use Core\Domains\Counter\Responses\CounterHistorySearchResponse;
use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\Services\HistoryChangesService;

readonly class CounterHistoryService
{
    public function __construct(
        private CounterHistoryFactory    $counterHistoryFactory,
        private CounterHistoryRepository $counterHistoryRepository,
        private HistoryChangesService    $historyChangesService,
    )
    {
    }

    public function getById(?int $id): ?CounterHistoryDTO
    {
        if ($id) {
            $model = $this->counterHistoryRepository->getById($id);
            if ($model) {
                return $this->counterHistoryFactory->makeDtoFromObject($model);
            }
        }

        return null;
    }

    public function save(CounterHistoryDTO $counterHistory): CounterHistoryDTO
    {
        $model = $this->counterHistoryRepository->getById($counterHistory->getId());
        if ($model) {
            $before = $this->counterHistoryFactory->makeDtoFromObject($model);
        }
        else {
            $before = new CounterHistoryDTO();
        }

        $model   = $this->counterHistoryRepository->save($this->counterHistoryFactory->makeModelFromDto($counterHistory, $model));
        $current = $this->counterHistoryFactory->makeDtoFromObject($model);

        $this->historyChangesService->writeToHistory(
            $counterHistory->getId() ? Event::UPDATE : Event::CREATE,
            HistoryType::COUNTER,
            $current->getCounterId(),
            HistoryType::COUNTER_HISTORY,
            $current->getId(),
            new CounterHistoryComparator($current),
            new CounterHistoryComparator($before),
            text: $counterHistory->getId() ? null : sprintf("Добавлено показание: %s", $counterHistory->getValue()),
        );

        if ( ! $counterHistory->getId()) {
            CounterHistoryCreatedEvent::dispatch($current);
        }
        else {
            CounterHistoryUpdatedEvent::dispatch($current, $before);
        }

        return $current;
    }

    public function search(CounterHistorySearcher $searcher): CounterHistorySearchResponse
    {
        $response = $this->counterHistoryRepository->search($searcher);

        $result = new CounterHistorySearchResponse();
        $result->setTotal($response->getTotal());

        $collection = new CounterHistoryCollection();
        foreach ($response->getItems() as $item) {
            $collection->add($this->counterHistoryFactory->makeDtoFromObject($item));
        }

        return $result->setItems($collection);
    }

    public function deleteById(int $id): bool
    {
        $history = $this->getById($id);

        if ( ! $history) {
            return false;
        }

        CounterHistoryDeletingEvent::dispatch($id);

        $this->historyChangesService->writeToHistory(
            Event::DELETE,
            HistoryType::COUNTER,
            $history->getCounterId(),
            HistoryType::COUNTER_HISTORY,
            $history->getId(),
        );

        return $this->counterHistoryRepository->deleteById($id);
    }


    public function getPrevious(CounterHistoryDTO $counterHistory): ?CounterHistoryDTO
    {
        $counterHistorySearcher = new CounterHistorySearcher();
        $counterHistorySearcher->setCounterId($counterHistory->getCounterId())
            ->setSortOrderProperty(CounterHistory::DATE, SearcherInterface::SORT_ORDER_ASC)
        ;

        $histories = $this->search($counterHistorySearcher)->getItems();

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
        $searcher = CounterHistorySearcher::make()
            ->setCounterId($counterId)
            ->setLimit(1)
            ->setWithClaim()
            ->setSortOrderProperty(CounterHistory::DATE, SearcherInterface::SORT_ORDER_DESC)
        ;

        return $this->search($searcher)->getItems()->first();
    }
}
