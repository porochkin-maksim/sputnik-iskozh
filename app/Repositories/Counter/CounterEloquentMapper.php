<?php declare(strict_types=1);

namespace App\Repositories\Counter;

use App\Models\Counter\Counter;
use App\Repositories\Account\AccountEloquentMapper;
use App\Repositories\CounterHistory\CounterHistoryEloquentMapper;
use App\Repositories\Files\FileEloquentMapper;
use Core\Domains\Counter\CounterCollection;
use Core\Domains\Counter\CounterEntity;
use Core\Domains\Counter\CounterTypeEnum;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Shared\Collections\Collection;
use IteratorAggregate;

class CounterEloquentMapper implements RepositoryDataMapperInterface
{
    public function __construct(
        private readonly FileEloquentMapper $fileEloquentMapper,
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
        $result = (new CounterEntity())
            ->setId($data->id)
            ->setType(CounterTypeEnum::tryFrom($data->type))
            ->setAccountId($data->account_id)
            ->setNumber($data->number)
            ->setIsInvoicing($data->is_invoicing)
            ->setIncrement($data->increment)
            ->setCreatedAt($data->created_at)
            ->setUpdatedAt($data->updated_at)
            ->setExpireAt($data->expire_at)
        ;

        if (isset($data->getRelations()[Counter::RELATION_HISTORY])) {
            $result->setHistoryCollection(app(CounterHistoryEloquentMapper::class)->makeEntityFromRepositoryDatas($data->getRelation(Counter::RELATION_HISTORY)));
        }

        if (isset($data->getRelations()[Counter::RELATION_ACCOUNT])) {
            $result->setAccount(app(AccountEloquentMapper::class)->makeEntityFromRepositoryData($data->getRelation(Counter::RELATION_ACCOUNT)));
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
