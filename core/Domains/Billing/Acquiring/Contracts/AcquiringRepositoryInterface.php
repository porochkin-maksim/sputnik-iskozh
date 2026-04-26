<?php declare(strict_types=1);

namespace Core\Domains\Billing\Acquiring\Contracts;

use Core\Domains\Billing\Acquiring\AcquiringEntity;
use Core\Domains\Billing\Acquiring\Models\AcquiringSearchResponse;
use Core\Repositories\SearcherInterface;

interface AcquiringRepositoryInterface
{
    public function search(SearcherInterface $searcher): AcquiringSearchResponse;

    public function save(AcquiringEntity $acquiring): AcquiringEntity;

    public function getById(?int $id): ?AcquiringEntity;

    public function getByIds(array $ids): AcquiringSearchResponse;

    public function deleteById(?int $id): bool;
}
