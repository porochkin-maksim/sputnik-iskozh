<?php declare(strict_types=1);

namespace Core\Domains\Billing\Period\Services;

use App\Models\Billing\Period;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Billing\Period\Models\PeriodDTO;
use Core\Domains\Billing\Period\Models\PeriodSearcher;
use Core\Domains\Billing\Period\Repositories\PeriodRepository;
use Core\Domains\Billing\Period\Responses\PeriodSearchResponse;

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

    public function save(PeriodDTO $period): PeriodDTO
    {
        return $this->periodRepository->save($period);
    }

    public function deleteById(?int $id): bool
    {
        return $this->periodRepository->deleteById($id);
    }

    public function getActive(): ?PeriodDTO
    {
        $searcher = new PeriodSearcher()
            ->setSortOrderProperty(Period::ID, SearcherInterface::SORT_ORDER_DESC)
            ->setLimit(1)
        ;

        return $this->search($searcher)->getItems()->first();
    }
}
