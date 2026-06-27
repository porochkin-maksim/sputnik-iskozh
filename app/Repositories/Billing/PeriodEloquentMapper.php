<?php declare(strict_types=1);

namespace App\Repositories\Billing;

use App\Models\Billing\Period;
use Core\Contracts\RepositoryDataMapperInterface;
use Core\Domains\Billing\Period\PeriodCollection;
use Core\Domains\Billing\Period\PeriodEntity;
use Core\Shared\Collections\Collection;
use IteratorAggregate;

readonly class PeriodEloquentMapper implements RepositoryDataMapperInterface
{
    public function makeRepositoryDataFromEntity($entity, $data = null): object
    {
        $result = $data ?: Period::make();

        $fill = [
            Period::NAME      => $entity->getName(),
            Period::START_AT  => $entity->getStartAt(),
            Period::END_AT    => $entity->getEndAt(),
            Period::IS_CLOSED => $entity->isClosed(),
        ];

        if ($entity->getClosedAt()) {
            $fill[Period::CLOSED_AT] = $entity->getClosedAt();
        }

        if ($entity->getClosedBy()) {
            $fill[Period::CLOSED_BY] = $entity->getClosedBy();
        }

        return $result->fill($fill);
    }

    public function makeEntityFromRepositoryData($data): object
    {
        $entity = new PeriodEntity()
            ->setId($data->{Period::ID})
            ->setName($data->{Period::NAME})
            ->setStartAt($data->{Period::START_AT})
            ->setEndAt($data->{Period::END_AT})
            ->setIsClosed((bool) $data->{Period::IS_CLOSED})
            ->setCreatedAt($data->{Period::CREATED_AT})
            ->setUpdatedAt($data->{Period::UPDATED_AT});

        if (isset($data->{Period::CLOSED_AT})) {
            $entity->setClosedAt($data->{Period::CLOSED_AT});
        }

        if (isset($data->{Period::CLOSED_BY})) {
            $entity->setClosedBy($data->{Period::CLOSED_BY});
        }

        return $entity;
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
