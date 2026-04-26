<?php declare(strict_types=1);

namespace App\Repositories\Billing;

use App\Models\Billing\Period;
use Core\Domains\Billing\Period\PeriodCollection;
use Core\Domains\Billing\Period\PeriodEntity;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Shared\Collections\Collection;
use IteratorAggregate;

readonly class PeriodEloquentMapper implements RepositoryDataMapperInterface
{
    public function makeRepositoryDataFromEntity($entity, $data = null): object
    {
        $result = $data ?: Period::make();

        return $result->fill([
            Period::NAME => $entity->getName(),
            Period::START_AT => $entity->getStartAt(),
            Period::END_AT => $entity->getEndAt(),
            Period::IS_CLOSED => $entity->isClosed(),
        ]);
    }

    public function makeEntityFromRepositoryData($data): object
    {
        return (new PeriodEntity())
            ->setId($data->{Period::ID})
            ->setName($data->{Period::NAME})
            ->setStartAt($data->{Period::START_AT})
            ->setEndAt($data->{Period::END_AT})
            ->setIsClosed((bool) $data->{Period::IS_CLOSED})
            ->setCreatedAt($data->{Period::CREATED_AT})
            ->setUpdatedAt($data->{Period::UPDATED_AT});
    }

    public function makeEntityFromRepositoryDatas(IteratorAggregate|array $datas): Collection
    {
        $collection = new PeriodCollection();

        foreach ($datas as $data) {
            $collection->add($this->makeEntityFromRepositoryData($data));
        }

        return $collection;
    }
}
