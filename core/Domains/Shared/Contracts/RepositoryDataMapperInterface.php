<?php declare(strict_types=1);

namespace Core\Domains\Shared\Contracts;

use Core\Shared\Collections\Collection;
use IteratorAggregate;

interface RepositoryDataMapperInterface
{
    public function makeRepositoryDataFromEntity($entity, $data = null): object;

    public function makeEntityFromRepositoryData($data): object;

    public function makeEntityFromRepositoryDatas(IteratorAggregate|array $datas): Collection;
}
