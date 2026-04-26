<?php declare(strict_types=1);

namespace Core\Domains\Billing\Period;

use App\Models\Billing\Period;
use Core\Repositories\SearcherInterface;

readonly class PeriodService
{
    public function __construct(
        private PeriodRepositoryInterface $periodRepository,
    )
    {
    }

    public function search(PeriodSearcher $searcher): PeriodSearchResponse
    {
        return $this->periodRepository->search($searcher);
    }

    public function getById(?int $id): ?PeriodEntity
    {
        return $this->periodRepository->getById($id);
    }

    public function save(PeriodEntity $period): PeriodEntity
    {
        return $this->periodRepository->save($period);
    }

    public function deleteById(?int $id): bool
    {
        return $this->periodRepository->deleteById($id);
    }

    public function getActive(): ?PeriodEntity
    {
        return $this->search(
            (new PeriodSearcher())
                ->setIsClosed(false)
                ->setSortOrderProperty(Period::ID, SearcherInterface::SORT_ORDER_DESC)
                ->setLimit(1),
        )->getItems()->first();
    }
}
