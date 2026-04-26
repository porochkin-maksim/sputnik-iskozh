<?php declare(strict_types=1);

namespace App\Repositories\Billing;

use App\Models\Billing\ClaimToObject;
use Core\Domains\Billing\ClaimToObject\ClaimObjectTypeEnum;
use Core\Domains\Billing\ClaimToObject\ClaimToObjectCollection;
use Core\Domains\Billing\ClaimToObject\ClaimToObjectEntity;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Shared\Collections\Collection;
use IteratorAggregate;

readonly class ClaimToObjectEloquentMapper implements RepositoryDataMapperInterface
{
    public function __construct(
        private ClaimEloquentMapper $claimEloquentMapper,
    )
    {
    }

    public function makeRepositoryDataFromEntity($entity, $data = null): object
    {
        return ($data ? : ClaimToObject::make())->fill([
            ClaimToObject::CLAIM_ID     => $entity->getClaimId(),
            ClaimToObject::REFERENCE_ID => $entity->getReferenceId(),
            ClaimToObject::TYPE         => $entity->getType()?->value,
        ]);
    }

    public function makeEntityFromRepositoryData($data): object
    {
        $result = new ClaimToObjectEntity();
        $result
            ->setId($data->{ClaimToObject::ID})
            ->setClaimId($data->{ClaimToObject::CLAIM_ID})
            ->setReferenceId($data->{ClaimToObject::REFERENCE_ID})
            ->setType(ClaimObjectTypeEnum::tryFrom($data->{ClaimToObject::TYPE}))
            ->setCreatedAt($data->{ClaimToObject::CREATED_AT})
            ->setUpdatedAt($data->{ClaimToObject::UPDATED_AT})
        ;

        if (isset($data->getRelations()[ClaimToObject::RELATION_CLAIM])) {
            $result->setClaim($this->claimEloquentMapper->makeEntityFromRepositoryData($data->getRelation(ClaimToObject::RELATION_CLAIM)));
        }

        return $result;
    }

    public function makeEntityFromRepositoryDatas(IteratorAggregate|array $datas): Collection
    {
        $result = new ClaimToObjectCollection();
        foreach ($datas as $data) {
            $result->add($this->makeEntityFromRepositoryData($data));
        }

        return $result;
    }
}
