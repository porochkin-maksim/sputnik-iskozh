<?php declare(strict_types=1);

namespace App\Repositories\Counter;

use App\Models\Counter\Counter;
use App\Repositories\Files\FileEloquentMapper;
use App\Repositories\Shared\Relations\CounterRelationAssembler;
use Core\Contracts\RepositoryDataMapperInterface;
use Core\Domains\Counter\CounterCollection;
use Core\Shared\Collections\Collection;
use IteratorAggregate;

class CounterEloquentMapper implements RepositoryDataMapperInterface
{
    public function __construct(
        private readonly FileEloquentMapper $fileEloquentMapper,
        private readonly CounterRelationAssembler $relationAssembler,
    )
    {
    }

    public function makeRepositoryDataFromEntity($entity, $data = null): object
    {
        $result = $data ? : Counter::make();
        $result->forceFill(['id' => $entity->getId()]);

        return $result->fill([
            Counter::TYPE         => $entity->getType()?->value,
            Counter::ACCOUNT_ID   => $entity->getAccountId(),
            Counter::NUMBER       => $entity->getNumber(),
            Counter::IS_INVOICING => $entity->isInvoicing(),
            Counter::INCREMENT    => $entity->getIncrement(),
            Counter::EXPIRE_AT    => $entity->getExpireAt(),
        ]);
    }

    public function makeEntityFromRepositoryData($data): object
    {
        $result = $this->relationAssembler->makeCounter($data);

        if (isset($data->getRelations()[Counter::RELATION_HISTORY])) {
            $result->setHistoryCollection($this->relationAssembler->makeHistoryCollection($data->getRelation(Counter::RELATION_HISTORY)));
        }

        if (isset($data->getRelations()[Counter::RELATION_ACCOUNT])) {
            $result->setAccount($this->relationAssembler->makeAccount($data->getRelation(Counter::RELATION_ACCOUNT)));
        }

        if (isset($data->getRelations()[Counter::RELATION_PASSPORT])) {
            $result->setPassportFile($this->fileEloquentMapper->makeEntityFromRepositoryData($data->getRelation(Counter::RELATION_PASSPORT)));
        }

        return $result;
    }

    public function makeEntityFromRepositoryDatas(IteratorAggregate|array $datas): Collection
    {
        $result = new CounterCollection();
        foreach ($datas as $data) {
            $result->add($this->makeEntityFromRepositoryData($data));
        }

        return $result;
    }
}
