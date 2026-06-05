<?php declare(strict_types=1);

namespace App\Repositories\Account;

use App\Models\Account\Account;
use App\Repositories\Infra\ExDataEloquentMapper;
use App\Repositories\Shared\Relations\AccountUserRelationAssembler;
use Core\Contracts\RepositoryDataMapperInterface;
use Core\Domains\Account\AccountCollection;
use Core\Domains\Account\AccountExDataEntity;
use Core\Domains\Account\AccountFactory;
use Core\Shared\Collections\Collection;
use Core\Shared\Helpers\DateTime\DateTimeHelper;
use IteratorAggregate;

class AccountEloquentMapper implements RepositoryDataMapperInterface
{
    public function __construct(
        private readonly AccountFactory               $factory,
        private readonly ExDataEloquentMapper         $exDataEloquentMapper,
        private readonly AccountUserRelationAssembler $relationAssembler,
    )
    {
    }

    public function makeRepositoryDataFromEntity($entity, $data = null): object
    {
        return ($data ? : Account::make())->fill([
            Account::NUMBER          => $entity->getNumber(),
            Account::SIZE            => $entity->getSize(),
            Account::BALANCE         => $entity->getBalance(),
            Account::IS_VERIFIED     => $entity->isVerified(),
            Account::PRIMARY_USER_ID => $entity->getPrimaryUserId(),
            Account::IS_INVOICING    => $entity->isInvoicing(),
            Account::SORT_VALUE      => $entity->getSortValue(),
        ]);
    }

    public function makeEntityFromRepositoryData($data): object
    {
        /** @var object $data */
        $result = $this->factory->makeDefault()
            ->setId($data->{Account::ID})
            ->setNumber($data->{Account::NUMBER})
            ->setSize($data->{Account::SIZE})
            ->setBalance($data->{Account::BALANCE})
            ->setIsVerified($data->{Account::IS_VERIFIED})
            ->setPrimaryUserId($data->{Account::PRIMARY_USER_ID})
            ->setIsInvoicing($data->{Account::IS_INVOICING})
            ->setSortValue($data->{Account::SORT_VALUE})
            ->setCreatedAt($data->{Account::CREATED_AT})
            ->setUpdatedAt($data->{Account::UPDATED_AT})
            ->setFraction($data->pivot?->fraction)
            ->setOwnerDate(DateTimeHelper::toCarbonOrNull($data->pivot?->ownerDate))
        ;

        if (isset($data->getRelations()[Account::RELATION_USERS])) {
            $result->setUsers($this->relationAssembler->makeUsers($data->getRelation(Account::RELATION_USERS)));
        }

        if (isset($data->getRelations()[Account::RELATION_EX_DATA])) {
            $exData        = $data->getRelation(Account::RELATION_EX_DATA);
            $exDataEntity  = $exData ? $this->exDataEloquentMapper->makeEntityFromRepositoryData($exData) : null;
            $accountExData = new AccountExDataEntity($exDataEntity?->getData());
            $result->setExData($accountExData)->getExData()->setId($exData->{Account::ID});
        }

        if ( ! $result->getExData()) {
            $result->setExData(new AccountExDataEntity());
        }

        return $result;
    }

    public function makeEntityFromRepositoryDatas(IteratorAggregate|array $datas): Collection
    {
        $result = new AccountCollection();
        foreach ($datas as $data) {
            $result->add($this->makeEntityFromRepositoryData($data));
        }

        return $result;
    }
}
