<?php declare(strict_types=1);

namespace Core\Domains\Billing\Period;

use Core\Repositories\SearcherInterface;

interface PeriodRepositoryInterface
{
    public function search(SearcherInterface $searcher): PeriodSearchResponse;

    public function save(PeriodEntity $period): PeriodEntity;

    public function getById(?int $id): ?PeriodEntity;

    public function getByIds(array $ids): PeriodSearchResponse;

    public function deleteById(?int $id): bool;
}
