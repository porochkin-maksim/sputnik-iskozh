<?php declare(strict_types=1);

namespace App\Repositories\Shared\Relations;

use App\Models\Account\Account as AccountModel;
use App\Models\User as UserModel;
use Core\Domains\Account\AccountCollection;
use Core\Domains\Account\AccountEntity;
use Core\Domains\Account\AccountFactory;
use Core\Domains\User\UserCollection;
use Core\Domains\User\UserEntity;
use Core\Domains\User\UserFactory;
use Core\Shared\Helpers\DateTime\DateTimeHelper;

readonly class AccountUserRelationAssembler
{
    public function __construct(
        private AccountFactory $accountFactory,
        private UserFactory    $userFactory,
    )
    {
    }

    public function makeUsers(iterable $users): UserCollection
    {
        $collection = new UserCollection();
        foreach ($users as $user) {
            $collection->add($this->makeUser($user));
        }

        return $collection;
    }

    public function makeAccounts(iterable $accounts): AccountCollection
    {
        $collection = new AccountCollection();
        foreach ($accounts as $account) {
            $collection->add($this->makeAccount($account));
        }

        return $collection;
    }

    private function makeUser(UserModel $data): UserEntity
    {
        return $this->userFactory->makeDefault()
            ->setId($data->id)
            ->setEmail($data->email)
            ->setPhone($data->phone)
            ->setFirstName($data->first_name)
            ->setMiddleName($data->middle_name)
            ->setLastName($data->last_name)
            ->setCreatedAt($data->created_at)
            ->setUpdatedAt($data->updated_at)
            ->setEmailVerifiedAt($data->email_verified_at)
            ->setFraction($data->pivot?->fraction)
            ->setOwnerDate(DateTimeHelper::toCarbonOrNull($data->pivot?->ownerDate))
            ->setIsDeleted($data->deleted_at)
        ;
    }

    private function makeAccount(AccountModel $data): AccountEntity
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
            ->setFraction($data->pivot?->fraction)
            ->setOwnerDate(DateTimeHelper::toCarbonOrNull($data->pivot?->ownerDate))
        ;
    }
}
