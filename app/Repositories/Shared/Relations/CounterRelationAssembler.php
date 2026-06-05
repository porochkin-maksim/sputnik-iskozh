<?php declare(strict_types=1);

namespace App\Repositories\Shared\Relations;

use App\Models\Account\Account as AccountModel;
use App\Models\Counter\Counter as CounterModel;
use App\Models\Counter\CounterHistory as CounterHistoryModel;
use Core\Domains\Account\AccountEntity;
use Core\Domains\Account\AccountFactory;
use Core\Domains\Counter\CounterCollection;
use Core\Domains\Counter\CounterEntity;
use Core\Domains\Counter\CounterFactory;
use Core\Domains\Counter\CounterTypeEnum;
use Core\Domains\CounterHistory\CounterHistoryCollection;
use Core\Domains\CounterHistory\CounterHistoryEntity;
use Core\Domains\CounterHistory\CounterHistoryFactory;

readonly class CounterRelationAssembler
{
    public function __construct(
        private CounterFactory $counterFactory,
        private CounterHistoryFactory $counterHistoryFactory,
        private AccountFactory $accountFactory,
    )
    {
    }

    public function makeHistoryCollection(iterable $histories): CounterHistoryCollection
    {
        $collection = new CounterHistoryCollection();
        foreach ($histories as $history) {
            $collection->add($this->makeHistory($history));
        }

        return $collection;
    }

    public function makeHistoryEntity(CounterHistoryModel $data): CounterHistoryEntity
    {
        return $this->makeHistory($data);
    }

    public function makeCounter(CounterModel $data): CounterEntity
    {
        $counter = $this->counterFactory->makeDefault()
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

        if (isset($data->getRelations()[CounterModel::RELATION_ACCOUNT])) {
            $counter->setAccount($this->makeAccount($data->getRelation(CounterModel::RELATION_ACCOUNT)));
        }

        return $counter;
    }

    public function makeAccount(AccountModel $data): AccountEntity
    {
        return $this->accountFactory->makeDefault()
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
        ;
    }

    private function makeHistory(CounterHistoryModel $data): CounterHistoryEntity
    {
        return $this->counterHistoryFactory->makeDefault()
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
    }
}
