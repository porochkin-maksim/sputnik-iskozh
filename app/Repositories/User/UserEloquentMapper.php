<?php declare(strict_types=1);

namespace App\Repositories\User;

use App\Models\Infra\UserInfo;
use App\Models\User;
use App\Repositories\Access\RoleEloquentMapper;
use App\Repositories\Account\AccountEloquentMapper;
use App\Repositories\Infra\ExDataEloquentMapper;
use Core\Domains\Account\AccountCollection;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Domains\User\UserCollection;
use Core\Domains\User\UserEntity;
use Core\Domains\User\UserExDataEntity;
use Core\Shared\Collections\Collection;
use Core\Shared\Helpers\DateTime\DateTimeHelper;
use Illuminate\Support\Facades\Hash;
use IteratorAggregate;

class UserEloquentMapper implements RepositoryDataMapperInterface
{
    public function __construct(
        private readonly RoleEloquentMapper   $roleEloquentMapper,
        private readonly ExDataEloquentMapper $exDataEloquentMapper,
    )
    {
    }

    public function makeRepositoryDataFromEntity($entity, $data = null): object
    {
        if ($data) {
            $result = $data;
        }
        else {
            $result = User::make();
        }

        if ($entity->getPassword()) {
            $result->setAttribute(User::PASSWORD, Hash::make($entity->getPassword()));
        }

        if ($entity->getEmailVerifiedAt()) {
            $result->forceFill([User::EMAIL_VERIFIED_AT => $entity->getEmailVerifiedAt()]);
        }

        return $result->fill([
            User::ID             => $entity->getId(),
            User::EMAIL          => $entity->getEmail(),
            User::PHONE          => $entity->getPhone(),
            User::LAST_NAME      => $entity->getLastName(),
            User::FIRST_NAME     => $entity->getFirstName(),
            User::MIDDLE_NAME    => $entity->getMiddleName(),
            User::REMEMBER_TOKEN => $entity->getRememberToken(),
        ]);
    }

    public function makeEntityFromRepositoryData($data): object
    {
        $result = (new UserEntity())
            ->setId($data->id)
            ->setEmail($data->email)
            ->setPhone($data->phone)
            ->setFirstName($data->first_name)
            ->setMiddleName($data->middle_name)
            ->setLastName($data->last_name)
            ->setCreatedAt($data->created_at)
            ->setUpdatedAt($data->updated_at)
            ->setEmailVerifiedAt($data->email_verified_at)
            ->setAccountId($data->account_id)
            ->setMembershipDate($data->{UserInfo::MEMBERSHIP_DATE})
            ->setMembershipDutyInfo($data->{UserInfo::MEMBERSHIP_DUTY_INFO})
            ->setFraction($data->pivot?->fraction)
            ->setOwnerDate(DateTimeHelper::toCarbonOrNull($data->pivot?->ownerDate))
            ->setIsDeleted($data->deleted_at)
        ;

        if (isset($data->getRelations()[User::ACCOUNTS])) {
            $accounts           = $data->getRelation(User::ACCOUNTS);
            $accountsCollection = new AccountCollection();
            foreach ($accounts as $account) {
                $accountsCollection->add(app(AccountEloquentMapper::class)->makeEntityFromRepositoryData($account));
            }
            $result->setAccounts($accountsCollection);
        }

        if (isset($data->getRelations()[User::EX_DATA])) {
            $exData    = $data->getRelation(User::EX_DATA);
            $exDataDTO = $exData ? $this->exDataEloquentMapper->makeEntityFromRepositoryData($exData) : null;

            $userExData = new UserExDataEntity($exDataDTO?->getData());
            $result->setExData($userExData)->getExData()->setId($exData?->id);
        }

        if ( ! $result->getExData()) {
            $result->setExData(new UserExDataEntity());
        }

        if (isset($data->getRelations()[User::ROLES])) {
            $role = $data->getRelation(User::ROLES)->first();
            $result->setRole($role
                ? $this->roleEloquentMapper->makeEntityFromRepositoryData($role)
                    ->setPermissions(array_map(
                        static fn($permission) => $permission->permission,
                        $role->permissions->all(),
                    ))
                : null);
        }

        return $result;
    }

    public function makeEntityFromRepositoryDatas(IteratorAggregate|array $datas): Collection
    {
        $result = new UserCollection();

        foreach ($datas as $data) {
            $result->add($this->makeEntityFromRepositoryData($data));
        }

        return $result;
    }
}
