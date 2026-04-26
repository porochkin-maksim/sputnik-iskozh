<?php declare(strict_types=1);

namespace App\Repositories\Option;

use App\Models\Infra\Option;
use Core\Domains\Option\Enums\OptionEnum;
use Core\Domains\Option\OptionCollection;
use Core\Domains\Option\OptionEntity;
use Core\Domains\Option\OptionFactory;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Shared\Collections\Collection;
use IteratorAggregate;

class OptionEloquentMapper implements RepositoryDataMapperInterface
{
    public function __construct(
        private readonly OptionFactory $optionFactory,
    )
    {
    }

    public function makeRepositoryDataFromEntity($entity, $data = null): object
    {
        if ( ! $data) {
            $data = Option::make();
        }

        return $data->fill([
            Option::ID => $entity->getId(),
            Option::DATA => $entity->getData()?->jsonSerialize(),
        ]);
    }

    public function makeEntityFromRepositoryData($data): object
    {
        $type = OptionEnum::tryFrom($data->{Option::ID});
        $entity = $type ? $this->optionFactory->makeByType($type) : new OptionEntity();

        if ($type && is_array($data->{Option::DATA})) {
            $entity->setData($this->optionFactory->makeDataDtoFromArray($type, $data->{Option::DATA}));
        } else {
            $entity->setData(null);
        }

        return $entity
            ->setId($data->{Option::ID})
            ->setType($type)
            ->setCreatedAt($data->{Option::CREATED_AT})
            ->setUpdatedAt($data->{Option::UPDATED_AT});
    }

    public function makeEntityFromRepositoryDatas(IteratorAggregate|array $datas): Collection
    {
        $result = new OptionCollection();
        foreach ($datas as $data) {
            $result->add($this->makeEntityFromRepositoryData($data));
        }

        return $result;
    }
}
