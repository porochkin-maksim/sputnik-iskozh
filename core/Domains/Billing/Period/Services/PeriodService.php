<?php declare(strict_types=1);

namespace Core\Domains\Billing\Period\Services;

use App\Models\Billing\Period;
use Carbon\Carbon;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Billing\Period\Models\PeriodDTO;
use Core\Domains\Billing\Period\Models\PeriodSearcher;
use Core\Domains\Billing\Period\Repositories\PeriodRepository;
use Core\Domains\Billing\Period\Responses\PeriodSearchResponse;
use Core\Enums\DateTimeFormat;

readonly class PeriodService
{
    public function __construct(
        private PeriodRepository $periodRepository,
    )
    {
    }

    public function search(PeriodSearcher $searcher): PeriodSearchResponse
    {
        return $this->periodRepository->search($searcher);
    }

    public function getById(?int $id): ?PeriodDTO
    {
        return $this->periodRepository->getById($id);
    }

    public function save(PeriodDTO $counter): PeriodDTO
    {
        return $this->periodRepository->save($counter);
    }

    public function deleteById(?int $id): bool
    {
        return $this->periodRepository->deleteById($id);
    }

    public function getCurrentPeriod(): ?PeriodDTO
    {
        $searcher = new PeriodSearcher();
        $searcher
            ->addWhere(Period::START_AT, SearcherInterface::LTE, Carbon::now()->format(DateTimeFormat::DATE_TIME_DEFAULT))
            ->addWhere(Period::END_AT, SearcherInterface::GTE, Carbon::now()->format(DateTimeFormat::DATE_TIME_DEFAULT))
        ;

        return $this->search($searcher)->getItems()->first();
    }

    public function getLast(): ?PeriodDTO
    {
        $searcher = new PeriodSearcher()
            ->setSortOrderProperty(Period::ID, SearcherInterface::SORT_ORDER_DESC)
            ->setLimit(1)
        ;

        return $this->search($searcher)->getItems()->first();
    }
}
