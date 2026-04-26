<?php declare(strict_types=1);

namespace App\Repositories\Account;

use App\Models\Account\Account;
use App\Repositories\Infra\ExDataEloquentMapper;
use App\Repositories\User\UserEloquentMapper;
use Core\Domains\Account\AccountCollection;
use Core\Domains\Account\AccountExDataEntity;
use Core\Domains\Account\AccountFactory;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Shared\Collections\Collection;
use Core\Shared\Helpers\DateTime\DateTimeHelper;
use IteratorAggregate;

class AccountEloquentMapper implements RepositoryDataMapperInterface
{
    public function __construct(
        private readonly AccountFactory       $factory,
        private readonly ExDataEloquentMapper $exDataEloquentMapper,
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
        $result = $this->factory->makeDefault()
            ->setId($data->id)
            ->setNumber($data->number)
            ->setSize($data->size)
            ->setBalance($data->balance)
            ->setIsVerified($data->is_verified)
            ->setPrimaryUserId($data->primary_user_id)
            ->setIsInvoicing($data->is_invoicing)
            ->setSortValue($data->sort_value)
            ->setCreatedAt($data->created_at)
            ->setUpdatedAt($data->updated_at)
            ->setFraction($data->pivot?->fraction)
            ->setOwnerDate(DateTimeHelper::toCarbonOrNull($data->pivot?->ownerDate))
        ;

        if (isset($data->getRelations()[Account::RELATION_USERS])) {
            foreach ($data->getRelation(Account::RELATION_USERS) as $user) {
                $result->addUser(app(UserEloquentMapper::class)->makeEntityFromRepositoryData($user));
            }
        }

        if (isset($data->getRelations()[Account::RELATION_EX_DATA])) {
            $exData        = $data->getRelation(Account::RELATION_EX_DATA);
            $exDataEntity  = $exData ? $this->exDataEloquentMapper->makeEntityFromRepositoryData($exData) : null;
            $accountExData = new AccountExDataEntity($exDataEntity?->getData());
            $result->setExData($accountExData)->getExData()->setId($exData->id);
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
