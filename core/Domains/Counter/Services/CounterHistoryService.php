<?php declare(strict_types=1);

namespace Core\Domains\Counter\Services;

use Core\Domains\Counter\Collections\CounterHistoryCollection;
use Core\Domains\Counter\Factories\CounterHistoryFactory;
use Core\Domains\Counter\Jobs\CreateTransactionForCounterChangeJob;
use Core\Domains\Counter\Models\CounterHistoryComparator;
use Core\Domains\Counter\Models\CounterHistoryDTO;
use Core\Domains\Counter\Models\CounterHistorySearcher;
use Core\Domains\Counter\Repositories\CounterHistoryRepository;
use Core\Domains\Counter\Responses\CounterHistorySearchResponse;
use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\Services\HistoryChangesService;
use Illuminate\Support\Facades\DB;

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

        if ($current->isVerified() !== $before->isVerified()) {
            CreateTransactionForCounterChangeJob::dispatch($current->getId());
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

        $this->historyChangesService->writeToHistory(
            Event::DELETE,
            HistoryType::COUNTER_HISTORY,
            $history->getId(),
        );

        return $this->counterHistoryRepository->deleteById($id);
    }
}
