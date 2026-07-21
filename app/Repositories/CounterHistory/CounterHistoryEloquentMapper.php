<?php declare(strict_types=1);

namespace App\Repositories\CounterHistory;

use App\Models\Billing\ClaimToObject;
use App\Models\Counter\CounterHistory;
use App\Repositories\Billing\ClaimEloquentMapper;
use App\Repositories\Files\FileEloquentMapper;
use App\Repositories\Shared\Relations\CounterRelationAssembler;
use Core\Contracts\RepositoryDataMapperInterface;
use Core\Domains\CounterHistory\CounterHistoryCollection;
use Core\Domains\CounterHistory\CounterHistoryEntity;
use Core\Shared\Collections\Collection;
use IteratorAggregate;

class CounterHistoryEloquentMapper implements RepositoryDataMapperInterface
{
    public function __construct(
        private readonly FileEloquentMapper       $fileEloquentMapper,
        private readonly CounterRelationAssembler $relationAssembler,
        private readonly ClaimEloquentMapper      $claimEloquentMapper,
    )
    {
    }

    public function makeRepositoryDataFromEntity($entity, $data = null): object
    {
        if ($entity->getPreviousId() === $entity->getId()) {
            $entity->setPreviousId(null);
        }
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

    /**
     * @param array<int, true> $visitedIds
     */
    public function makeEntityFromRepositoryData($data, array $visitedIds = []): object
    {
        $result = $this->relationAssembler->makeHistoryEntity($data);
        $currentId = $result->getId();

        if (
            isset($data->getRelations()[CounterHistory::RELATION_PREVIOUS])
            && $currentId !== null
            && ! isset($visitedIds[$currentId])
        ) {
            $visitedIds[$currentId] = true;
            $result->setPrevious(
                $this->makeEntityFromRepositoryData(
                    $data->getRelation(CounterHistory::RELATION_PREVIOUS),
                    $visitedIds,
                ),
            );
        }

        if (isset($data->getRelations()[CounterHistory::RELATION_FILE])) {
            $result->setFile($this->fileEloquentMapper->makeEntityFromRepositoryData($data->getRelation(CounterHistory::RELATION_FILE)));
        }

        if (isset($data->getRelations()[CounterHistory::RELATION_COUNTER])) {
            $result->setCounter($this->relationAssembler->makeCounter($data->getRelation(CounterHistory::RELATION_COUNTER)));
        }

        if (isset($data->getRelations()[CounterHistory::RELATION_CLAIM])) {
            $claim = $data->getRelation(CounterHistory::RELATION_CLAIM);
            if (isset($claim->getRelations()[ClaimToObject::RELATION_CLAIM])) {
                $result->setClaim($this->claimEloquentMapper->makeEntityFromRepositoryData($claim->getRelation(ClaimToObject::RELATION_CLAIM)));
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
