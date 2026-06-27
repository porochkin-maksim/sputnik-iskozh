<?php declare(strict_types=1);

namespace App\Repositories\Billing;

use App\Models\Billing\Service;
use Core\Contracts\RepositoryDataMapperInterface;
use Core\Domains\Billing\Service\ServiceCollection;
use Core\Domains\Billing\Service\ServiceEntity;
use Core\Domains\Billing\Service\ServiceTypeEnum;
use Core\Shared\Collections\Collection;
use IteratorAggregate;

readonly class ServiceEloquentMapper implements RepositoryDataMapperInterface
{
    public function __construct(
        private PeriodEloquentMapper $periodEloquentMapper,
    )
    {
    }

    public function makeRepositoryDataFromEntity($entity, $data = null): object
    {
        $result = $data ?: Service::make();

        return $result->fill([
            Service::TYPE => $entity->getType()?->value,
            Service::PERIOD_ID => $entity->getPeriodId(),
            Service::PERIOD_FROM => $entity->getPeriodFrom(),
            Service::PERIOD_TO => $entity->getPeriodTo(),
            Service::NAME => $entity->getName(),
            Service::COST => $entity->getCost(),
        ]);
    }

    public function makeEntityFromRepositoryData($data): object
    {
        $service = new ServiceEntity()
            ->setId($data->{Service::ID})
            ->setType(ServiceTypeEnum::tryFrom($data->{Service::TYPE}))
            ->setPeriodId($data->{Service::PERIOD_ID})
            ->setPeriodFrom($data->{Service::PERIOD_FROM})
            ->setPeriodTo($data->{Service::PERIOD_TO})
            ->setName($data->{Service::NAME})
            ->setCost($data->{Service::COST})
            ->setCreatedAt($data->{Service::CREATED_AT})
            ->setUpdatedAt($data->{Service::UPDATED_AT});

        if (isset($data->getRelations()[Service::RELATION_PERIOD])) {
            $service->setPeriod($this->periodEloquentMapper->makeEntityFromRepositoryData($data->getRelation(Service::RELATION_PERIOD)));
        }

        return $service;
    }

    public function makeEntityFromRepositoryDatas(IteratorAggregate|array $datas): Collection
    {
        $collection = new ServiceCollection();

        foreach ($datas as $data) {
            $collection->add($this->makeEntityFromRepositoryData($data));
        }

        return $collection;
    }
}
