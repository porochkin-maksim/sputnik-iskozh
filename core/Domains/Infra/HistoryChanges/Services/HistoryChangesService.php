<?php declare(strict_types=1);

namespace Core\Domains\Infra\HistoryChanges\Services;

use Core\Domains\Infra\Comparator\DTO\AbstractComparatorDTO;
use Core\Domains\Infra\Comparator\Services\Comparator;
use Core\Domains\Infra\HistoryChanges\Collections\HistoryChangesCollection;
use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\Factories\HistoryChangesFactory;
use Core\Domains\Infra\HistoryChanges\Jobs\CreateHistoryJob;
use Core\Domains\Infra\HistoryChanges\Models\HistoryChangesDTO;
use Core\Domains\Infra\HistoryChanges\Models\LogData;
use Core\Domains\Infra\HistoryChanges\Repositories\HistoryChangesRepository;
use Core\Domains\Infra\HistoryChanges\Responses\SearchResponse;
use Core\Domains\Infra\HistoryChanges\Models\HistoryChangesSearcher;

readonly class HistoryChangesService
{
    public function __construct(
        private HistoryChangesFactory    $historyChangesFactory,
        private HistoryChangesRepository $historyChangesRepository,
        private Comparator               $comparator,
    )
    {
    }

    public function makeHistory(): HistoryChangesDTO
    {
        return $this->historyChangesFactory->makeDefault();
    }

    public function writeToHistory(
        Event                  $event,
        HistoryType            $type,
        ?int                   $primaryId,
        ?HistoryType           $referenceType = null,
        ?int                   $referenceId = null,
        ?AbstractComparatorDTO $current = null,
        ?AbstractComparatorDTO $before = null,
        ?string                $text = null,
    ): void
    {
        if ($current && $before) {
            $changes = $this->comparator->makeChanges($before, $current);
        }

        if (
            ($event === Event::CREATE || $event === Event::UPDATE)
            && isset($changes) && ! $changes->count()
        ) {
            return;
        }

        $logData = new LogData($event, $changes ?? null, $text);

        $historyChanges = $this->historyChangesFactory->makeDefault();
        $historyChanges
            ->setType($type)
            ->setReferenceType($referenceType)
            ->setPrimaryId($primaryId)
            ->setReferenceId($referenceId)
            ->setLog($logData);

        CreateHistoryJob::dispatch($historyChanges);
    }

    public function save(HistoryChangesDTO $historyChanges): HistoryChangesDTO
    {
        $model = $this->historyChangesRepository->getById($historyChanges->getId());

        $model = $this->historyChangesRepository->save($this->historyChangesFactory->makeModelFromDto($historyChanges, $model));

        return $this->historyChangesFactory->makeDtoFromObject($model);
    }

    public function search(HistoryChangesSearcher $searcher): SearchResponse
    {
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
