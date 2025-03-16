<?php declare(strict_types=1);

namespace Core\Domains\Counter\Services;

use App\Models\Counter\Counter;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Counter\Collections\CounterCollection;
use Core\Domains\Counter\Factories\CounterFactory;
use Core\Domains\Counter\Models\CounterComparator;
use Core\Domains\Counter\Models\CounterDTO;
use Core\Domains\Counter\Models\CounterSearcher;
use Core\Domains\Counter\Repositories\CounterRepository;
use Core\Domains\Counter\Responses\CounterSearchResponse;
use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\Services\HistoryChangesService;

readonly class CounterService
{
    public function __construct(
        private CounterFactory        $counterFactory,
        private CounterRepository     $counterRepository,
        private HistoryChangesService $historyChangesService,
    )
    {
    }

    public function search(CounterSearcher $searcher): CounterSearchResponse
    {
        $response = $this->counterRepository->search($searcher);

        $result = new CounterSearchResponse();
        $result->setTotal($response->getTotal());

        $collection = new CounterCollection();
        foreach ($response->getItems() as $item) {
            $collection->add($this->counterFactory->makeDtoFromObject($item));
        }

        return $result->setItems($collection);
    }

    public function getById(int $id): ?CounterDTO
    {
        $searcher = new CounterSearcher();
        $searcher->setId($id);

        return $this->search($searcher)->getItems()->first();
    }

    public function save(CounterDTO $counter): CounterDTO
    {
        $model = $this->counterRepository->getById($counter->getId());
        if ($model) {
            $before = $this->counterFactory->makeDtoFromObject($model);
        }
        else {
            $before = new CounterDTO();
        }

        $model   = $this->counterRepository->save($this->counterFactory->makeModelFromDto($counter, $model));
        $current = $this->counterFactory->makeDtoFromObject($model);

        $this->historyChangesService->writeToHistory(
            $counter->getId() ? Event::UPDATE : Event::CREATE,
            HistoryType::COUNTER,
            $current->getId(),
            null,
            null,
            new CounterComparator($current),
            new CounterComparator($before),
        );

        return $current;
    }

    public function getByAccountId(?int $id): CounterCollection
    {
        $searcher = new CounterSearcher();//
        $searcher
            ->setAccountId($id)
            ->setSortOrderProperty(Counter::IS_INVOICING, SearcherInterface::SORT_ORDER_DESC)
            ->setSortOrderProperty(Counter::ID, SearcherInterface::SORT_ORDER_DESC)
        ;

        return $this->search($searcher)->getItems();
    }
}
