<?php declare(strict_types=1);

namespace Core\Domains\HistoryChanges;

use Core\Domains\HistoryChanges\Jobs\CreateHistoryJob;
use Core\Domains\Infra\Comparator\DTO\AbstractComparatorDTO;
use Core\Domains\Infra\Comparator\DTO\ChangesCollection;
use Core\Domains\Infra\Comparator\Services\Comparator;

class HistoryChangesService
{
    public function __construct(
        private HistoryChangesFactory $historyChangesFactory,
        private HistoryChangesRepositoryInterface $historyChangesRepository,
        private Comparator $comparator,
    )
    {
    }

    public function makeHistory(): HistoryChangesEntity
    {
        return $this->historyChangesFactory->makeDefault();
    }

    public function logChanges(
        Event             $event,
        HistoryType       $type,
        ChangesCollection $changes,
        ?int              $primaryId,
        ?HistoryType      $referenceType = null,
        ?int              $referenceId = null,
    ): void
    {
        $logData = new LogData($event, $changes, null);

        $historyChanges = $this->historyChangesFactory->makeDefault();
        $historyChanges
            ->setType($type)
            ->setReferenceType($referenceType)
            ->setPrimaryId($primaryId)
            ->setReferenceId($referenceId)
            ->setLog($logData)
        ;

        CreateHistoryJob::dispatch($historyChanges);
    }

    public function writeToHistory(
        Event                  $event,
        HistoryType            $primaryType,
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
            ->setType($primaryType)
            ->setReferenceType($referenceType)
            ->setPrimaryId($primaryId)
            ->setReferenceId($referenceId)
            ->setLog($logData)
        ;

        CreateHistoryJob::dispatch($historyChanges);
    }

    public function save(HistoryChangesEntity $historyChanges): HistoryChangesEntity
    {
        return $this->historyChangesRepository->save($historyChanges);
    }

    public function search(HistoryChangesSearcher $searcher): HistoryChangesSearchResponse
    {
        return $this->historyChangesRepository->search($searcher);
    }
}
