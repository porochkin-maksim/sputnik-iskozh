<?php declare(strict_types=1);

namespace App\Repositories\Infra;

use App\Models\Infra\ExData;
use Core\Domains\Infra\ExData\ExDataEntity;
use Core\Domains\Infra\ExData\Enums\ExDataTypeEnum;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
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

    public function makeEntityFromRepositoryData($data): object
    {
        return (new ExDataEntity())
            ->setId($data->id)
            ->setType(ExDataTypeEnum::tryFrom($data->type))
            ->setReferenceId($data->reference_id)
            ->setData($data->data)
            ->setCreatedAt($data->created_at)
            ->setUpdatedAt($data->updated_at)
        ;
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
