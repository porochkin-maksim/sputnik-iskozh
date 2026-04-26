<?php declare(strict_types=1);

namespace App\Repositories\CounterHistory;

use App\Models\Billing\ClaimToObject;
use App\Models\Counter\CounterHistory;
use App\Repositories\Billing\ClaimEloquentMapper;
use App\Repositories\Counter\CounterEloquentMapper;
use App\Repositories\Files\FileEloquentMapper;
use Core\Domains\CounterHistory\CounterHistoryCollection;
use Core\Domains\CounterHistory\CounterHistoryEntity;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Shared\Collections\Collection;
use IteratorAggregate;

class CounterHistoryEloquentMapper implements RepositoryDataMapperInterface
{
    public function __construct(
        private readonly FileEloquentMapper $fileEloquentMapper,
    )
    {
    }

    public function makeRepositoryDataFromEntity($entity, $data = null): object
    {
        $result = $data ? : CounterHistory::make();
        $result->forceFill(['id' => $entity->getId()]);

        return $result->fill([
            CounterHistory::COUNTER_ID     => $entity->getCounterId(),
            CounterHistory::PREVIOUS_ID    => $entity->getPreviousId(),
            CounterHistory::PREVIOUS_VALUE => $entity->getPreviousValue(),
            CounterHistory::VALUE          => $entity->getValue(),
            CounterHistory::DATE           => $entity->getDate(),
            CounterHistory::IS_VERIFIED    => $entity->isVerified(),
        ]);
    }

    public function makeEntityFromRepositoryData($data): object
    {
        $result = (new CounterHistoryEntity())
            ->setId($data->id)
            ->setCounterId($data->counter_id)
            ->setPreviousId($data->previous_id)
            ->setPreviousValue($data->previous_value)
            ->setValue($data->value)
            ->setDate($data->date)
            ->setIsVerified($data->is_verified)
            ->setCreatedAt($data->created_at)
            ->setUpdatedAt($data->updated_at)
        ;

        if (isset($data->getRelations()[CounterHistory::RELATION_PREVIOUS])) {
            $result->setPrevious($this->makeEntityFromRepositoryData($data->getRelation(CounterHistory::RELATION_PREVIOUS)));
        }

        if (isset($data->getRelations()[CounterHistory::RELATION_FILE])) {
            $result->setFile($this->fileEloquentMapper->makeEntityFromRepositoryData($data->getRelation(CounterHistory::RELATION_FILE)));
        }

        if (isset($data->getRelations()[CounterHistory::RELATION_COUNTER])) {
            $result->setCounter(app(CounterEloquentMapper::class)->makeEntityFromRepositoryData($data->getRelation(CounterHistory::RELATION_COUNTER)));
        }

        if (isset($data->getRelations()[CounterHistory::RELATION_CLAIM])) {
            $claim = $data->getRelation(CounterHistory::RELATION_CLAIM);
            if (isset($claim->getRelations()[ClaimToObject::RELATION_CLAIM])) {
                $result->setClaim(app(ClaimEloquentMapper::class)->makeEntityFromRepositoryData($claim->getRelation(ClaimToObject::RELATION_CLAIM)));
            }
        }

        return $result;
    }

    public function makeEntityFromRepositoryDatas(IteratorAggregate|array $datas): Collection
    {
        $result = new CounterHistoryCollection();
        foreach ($datas as $data) {
            $result->add($this->makeEntityFromRepositoryData($data));
        }

        return $result;
    }
}
