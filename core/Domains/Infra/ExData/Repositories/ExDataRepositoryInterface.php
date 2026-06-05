<?php declare(strict_types=1);

namespace Core\Domains\Infra\ExData\Repositories;

use Core\Domains\Infra\ExData\ExDataEntity;
use Core\Repositories\BaseSearchResponse;
use Core\Repositories\SearcherInterface;

interface ExDataRepositoryInterface
{
    public function search(SearcherInterface $searcher): BaseSearchResponse;

    public function save(ExDataEntity $entity): ExDataEntity;

    public function getById(?int $id): ?ExDataEntity;

    public function deleteById(?int $id): bool;
}
