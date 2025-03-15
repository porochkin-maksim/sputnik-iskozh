<?php declare(strict_types=1);

namespace Core\Domains\Billing\Period\Services;

use App\Models\Billing\Period;
use Carbon\Carbon;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Billing\Period\Collections\PeriodCollection;
use Core\Domains\Billing\Period\Events\PeriodCreatedEvent;
use Core\Domains\Billing\Period\Factories\PeriodFactory;
use Core\Domains\Billing\Period\Models\PeriodComparator;
use Core\Domains\Billing\Period\Models\PeriodDTO;
use Core\Domains\Billing\Period\Models\PeriodSearcher;
use Core\Domains\Billing\Period\Repositories\PeriodRepository;
use Core\Domains\Billing\Period\Responses\SearchResponse;
use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\Services\HistoryChangesService;
use Core\Enums\DateTimeFormat;

readonly class PeriodService
{
    public function __construct(
        private PeriodFactory         $periodFactory,
        private PeriodRepository      $periodRepository,
        private HistoryChangesService $historyChangesService,
    )
    {
    }

    public function save(PeriodDTO $period): PeriodDTO
    {
        $model = $this->periodRepository->getById($period->getId());
        if ($model) {
            $before = $this->periodFactory->makeDtoFromObject($model);
        }
        else {
            $before = new PeriodDTO();
        }

        $model   = $this->periodRepository->save($this->periodFactory->makeModelFromDto($period, $model));
        $current = $this->periodFactory->makeDtoFromObject($model);

        $this->historyChangesService->writeToHistory(
            $period->getId() ? Event::UPDATE : Event::CREATE,
            HistoryType::PERIOD,
            $current->getId(),
            null,
            null,
            new PeriodComparator($current),
            new PeriodComparator($before),
        );

        if ( ! $period->getId()) {
            PeriodCreatedEvent::dispatch($current->getId());
            $current = $this->getById($current->getId());
        }

        return $current;
    }

    public function search(PeriodSearcher $searcher): SearchResponse
    {
        $response = $this->periodRepository->search($searcher);

        $result = new SearchResponse();
        $result->setTotal($response->getTotal());

        $collection = new PeriodCollection();
        foreach ($response->getItems() as $item) {
            $collection->add($this->periodFactory->makeDtoFromObject($item));
        }

        return $result->setItems($collection);
    }

    public function getById(?int $id): ?PeriodDTO
    {
        if ( ! $id) {
            return null;
        }

        $searcher = new PeriodSearcher();
        $searcher->setId($id);
        $result = $this->periodRepository->search($searcher)->getItems()->first();

        return $result ? $this->periodFactory->makeDtoFromObject($result) : null;
    }

    public function deleteById(int $id): bool
    {
        $period = $this->getById($id);

        if ( ! $period) {
            return false;
        }

        $this->historyChangesService->writeToHistory(
            Event::DELETE,
            HistoryType::PERIOD,
            $period->getId(),
        );

        return $this->periodRepository->deleteById($id);
    }

    public function getCurrentPeriod(): ?PeriodDTO
    {
        $searcher = new PeriodSearcher();
        $searcher
            ->addWhere(Period::START_AT, SearcherInterface::LTE, Carbon::now()->format(DateTimeFormat::DATE_TIME_DEFAULT))
           ->addWhere(Period::END_AT, SearcherInterface::GTE, Carbon::now()->format(DateTimeFormat::DATE_TIME_DEFAULT))
        ;

        $result = $this->periodRepository->search($searcher)->getItems()->first();

        return $result ? $this->periodFactory->makeDtoFromObject($result) : null;
    }
}
