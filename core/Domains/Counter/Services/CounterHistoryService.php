<?php declare(strict_types=1);

namespace Core\Domains\Counter\Services;

use App\Models\Counter\Counter;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Counter\Collections\CounterHistoryCollection;
use Core\Domains\Counter\CounterLocator;
use Core\Domains\Counter\Events\CounterHistoryCreatedEvent;
use Core\Domains\Counter\Factories\CounterHistoryFactory;
use Core\Domains\Counter\Models\CounterHistoryComparator;
use Core\Domains\Counter\Models\CounterHistoryDTO;
use Core\Domains\Counter\Models\CounterHistorySearcher;
use Core\Domains\Counter\Models\CounterSearcher;
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
            CounterHistoryCreatedEvent::dispatch($current->getId());
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

        if ($history->getFile()) {
            CounterLocator::FileService()->deleteById($history->getFile()->getId());
        }

        $this->historyChangesService->writeToHistory(
            Event::DELETE,
            HistoryType::COUNTER_HISTORY,
            $history->getId(),
        );

        return $this->counterHistoryRepository->deleteById($id);
    }


    public function getPrevios(CounterHistoryDTO $counterHistory): ?CounterHistoryDTO
    {
        $counterSearcher = new CounterSearcher();
        $counterSearcher
            ->setId($counterHistory->getCounterId())
            ->addWhere(Counter::IS_INVOICING, SearcherInterface::EQUALS, true)
        ;
        $counter = CounterLocator::CounterService()->search($counterSearcher)->getItems()->first();

        $result = null;
        if ($counter) {
            foreach ($counter->getHistoryCollection()->sortById() as $item) {
                if ((int) $item->getId() >= (int) $counterHistory->getId()) {
                    break;
                }

                $result = $item;
            }
        }

        return $result;
    }
}
