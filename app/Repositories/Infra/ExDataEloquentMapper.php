<?php declare(strict_types=1);

namespace App\Repositories\Infra;

use App\Models\Infra\ExData;
use Core\Contracts\RepositoryDataMapperInterface;
use Core\Domains\Infra\ExData\Enums\ExDataTypeEnum;
use Core\Domains\Infra\ExData\ExDataEntity;
use Core\Shared\Collections\Collection;
use IteratorAggregate;

class ExDataEloquentMapper implements RepositoryDataMapperInterface
{
    public function makeRepositoryDataFromEntity($entity, $data = null): object
    {
        return ($data ? : ExData::make())->fill([
            ExData::ID           => $entity->getId(),
            ExData::TYPE         => $entity->getType()?->value,
            ExData::REFERENCE_ID => $entity->getReferenceId(),
            ExData::DATA         => $entity->getData(),
        ]);
    }

    /**
     * @var ExData $data
     */
    public function makeEntityFromRepositoryData($data): object
    {
        /** @var object $data */
        $entity = new ExDataEntity();
        $entity->setId($data->{ExData::ID});
        $entity->setType(ExDataTypeEnum::tryFrom($data->{ExData::TYPE}));
        $entity->setReferenceId($data->{ExData::REFERENCE_ID});
        $entity->setData($data->{ExData::DATA});
        $entity->setCreatedAt($data->{ExData::CREATED_AT});
        $entity->setUpdatedAt($data->{ExData::UPDATED_AT});

        return $entity;
    }

    public function makeEntityFromRepositoryDatas(IteratorAggregate|array $datas): Collection
    {
        $result = new Collection();
        foreach ($datas as $data) {
            $result->add($this->makeEntityFromRepositoryData($data));
        }

        return $result;
    }
}
