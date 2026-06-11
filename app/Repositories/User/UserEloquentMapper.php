<?php declare(strict_types=1);

namespace App\Repositories\User;

use App\Models\Infra\UserInfo;
use App\Models\User;
use App\Repositories\Access\RoleEloquentMapper;
use App\Repositories\Infra\ExDataEloquentMapper;
use App\Repositories\Shared\Relations\AccountUserRelationAssembler;
use Core\Contracts\RepositoryDataMapperInterface;
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
        private readonly RoleEloquentMapper           $roleEloquentMapper,
        private readonly ExDataEloquentMapper         $exDataEloquentMapper,
        private readonly AccountUserRelationAssembler $relationAssembler,
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
            User::LOGGED_IN_AT   => $entity->getLoggedInAt(),
        ]);
    }

    public function makeEntityFromRepositoryData($data): object
    {
        /** @var object $data */
        $result = (new UserEntity())
            ->setId($data->{User::ID})
            ->setEmail($data->{User::EMAIL})
            ->setPhone($data->{User::PHONE})
            ->setFirstName($data->{User::FIRST_NAME})
            ->setMiddleName($data->{User::MIDDLE_NAME})
            ->setLastName($data->{User::LAST_NAME})
            ->setCreatedAt($data->{User::CREATED_AT})
            ->setUpdatedAt($data->{User::UPDATED_AT})
            ->setEmailVerifiedAt($data->{User::EMAIL_VERIFIED_AT})
            ->setLoggedInAt($data->{User::LOGGED_IN_AT})
            ->setAccountId($data->{User::ACCOUNT_ID})
            ->setMembershipDate($data->{UserInfo::MEMBERSHIP_DATE})
            ->setMembershipDutyInfo($data->{UserInfo::MEMBERSHIP_DUTY_INFO})
            ->setFraction($data->pivot?->fraction)
            ->setOwnerDate(DateTimeHelper::toCarbonOrNull($data->pivot?->ownerDate))
            ->setIsDeleted($data->deleted_at)
        ;

        if (isset($data->getRelations()[User::ACCOUNTS])) {
            $result->setAccounts($this->relationAssembler->makeAccounts($data->getRelation(User::ACCOUNTS)));
        }

        if (isset($data->getRelations()[User::EX_DATA])) {
            $exData    = $data->getRelation(User::EX_DATA);
            $exDataDTO = $exData ? $this->exDataEloquentMapper->makeEntityFromRepositoryData($exData) : null;

            $userExData = new UserExDataEntity($exDataDTO?->getData());
            $result->setExData($userExData)->getExData()->setId($exData?->{User::ID});
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
