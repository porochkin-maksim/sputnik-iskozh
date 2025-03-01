<?php declare(strict_types=1);

namespace Core\Domains\Infra\HistoryChanges\Services;

use App\Models\Infra\HistoryChanges;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Infra\Comparator\DTO\AbstractComparatorDTO;
use Core\Domains\Infra\Comparator\Services\Comparator;
use Core\Domains\Infra\HistoryChanges\Collections\HistoryChangesCollection;
use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\Enums\Type;
use Core\Domains\Infra\HistoryChanges\Factories\HistoryChangesFactory;
use Core\Domains\Infra\HistoryChanges\Models\HistoryChangesDTO;
use Core\Domains\Infra\HistoryChanges\Models\HistorySearcher;
use Core\Domains\Infra\HistoryChanges\Models\LogData;
use Core\Domains\Infra\HistoryChanges\Repositories\HistoryChangesRepository;
use Core\Domains\Infra\HistoryChanges\Responses\SearchResponse;

readonly class HistoryChangesService
{
    public function __construct(
        private HistoryChangesFactory    $historyChangesFactory,
        private HistoryChangesRepository $historyChangesRepository,
        private Comparator               $comparator,
    )
    {
    }

    public function writeToHistory(Event $event, AbstractComparatorDTO $current, ?AbstractComparatorDTO $before = null): void
    {
        if ($before) {
            $changes = $this->comparator->makeChanges($before, $current);
        }

        if (
            ($event === Event::CREATE || $event === Event::UPDATE)
            && isset($changes) && ! $changes->count()
        ) {
            return;
        }

        $logData = new LogData($event, $changes ?? null);

        $historyChanges = $this->historyChangesFactory->makeDefault();
        $historyChanges
            ->setPrimaryId($current->getId())
            ->setType(Type::makeTypeByClass(get_class($current)))
            ->setLog($logData);

        $this->save($historyChanges);
    }

    private function save(HistoryChangesDTO $historyChanges): HistoryChangesDTO
    {
        $model = $this->historyChangesRepository->getById($historyChanges->getId());

        $model = $this->historyChangesRepository->save($this->historyChangesFactory->makeModelFromDto($historyChanges, $model));

        return $this->historyChangesFactory->makeDtoFromObject($model);
    }

    public function search(int $type, int $primaryId, ?int $referenceId): SearchResponse
    {
        $searcher = new HistorySearcher();
        $searcher
            ->setMainFilters($type, $primaryId, $referenceId)
            ->setSortOrderProperty(HistoryChanges::CREATED_AT, SearcherInterface::SORT_ORDER_DESC);

        $response = $this->historyChangesRepository->search($searcher);

        $result = new SearchResponse();
        $result->setTotal($response->getTotal());

        $collection = new HistoryChangesCollection();
        foreach ($response->getItems() as $item) {
            $collection->add($this->historyChangesFactory->makeDtoFromObject($item));
        }

        return $result->setItems($collection);
    }
}
