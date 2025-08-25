<?php declare(strict_types=1);

namespace Core\Domains\User\Factories;

use App\Models\Infra\UserInfo;
use App\Models\User;
use Core\Domains\Access\RoleLocator;
use Core\Domains\Account\AccountLocator;
use Core\Domains\Account\Collections\AccountCollection;
use Core\Domains\Infra\ExData\ExDataLocator;
use Core\Domains\User\Enums\UserIdEnum;
use Core\Domains\User\Models\UserDTO;
use Core\Domains\User\Models\UserExDataDTO;
use Illuminate\Support\Facades\Hash;

readonly class UserFactory
{
    public function makeDefault(): UserDTO
    {
        return new UserDTO();
    }

    public function makeUndefined(): UserDTO
    {
        $result = new UserDTO();
        $result
            ->setId(UserIdEnum::UNDEFINED)
            ->setLastName('Неавторизованный')
        ;

        return $result;
    }

    public function makeRobot(): UserDTO
    {
        $result = new UserDTO();
        $result
            ->setId(UserIdEnum::ROBOT)
            ->setLastName('Робот')
        ;

        return $result;
    }

    private function encryptPassword(?string $password): ?string
    {
        return Hash::make($password);
    }

    public function makeModelFromDto(UserDTO $dto, ?User $model = null): User
    {
        if ($model) {
            $result = $model;
        }
        else {
            $result = User::make();
        }

        if ($dto->getPassword()) {
            $result->setAttribute(User::PASSWORD, $this->encryptPassword($dto->getPassword()));
        }

        if ($dto->getEmailVerifiedAt()) {
            $result->forceFill([User::EMAIL_VERIFIED_AT => $dto->getEmailVerifiedAt()]);
        }

        return $result->fill([
            User::ID             => $dto->getId(),
            User::EMAIL          => $dto->getEmail(),
            User::PHONE          => $dto->getPhone(),
            User::LAST_NAME      => $dto->getLastName(),
            User::FIRST_NAME     => $dto->getFirstName(),
            User::MIDDLE_NAME    => $dto->getMiddleName(),
            User::REMEMBER_TOKEN => $dto->getRememberToken(),
        ]);
    }

    public function makeDtoFromObject(?User $model): UserDTO
    {
        if ( ! $model) {
            $model = User::make();
        }
        $result = new UserDTO($model);

        $result
            ->setId($model->id)
            ->setEmail($model->email)
            ->setPhone($model->phone)
            ->setFirstName($model->first_name)
            ->setMiddleName($model->middle_name)
            ->setLastName($model->last_name)
            ->setCreatedAt($model->created_at)
            ->setUpdatedAt($model->updated_at)
            ->setEmailVerifiedAt($model->email_verified_at)
            ->setAccountId($model->account_id)
            ->setOwnershipDate($model->{UserInfo::OWNERSHIP_DATE})
            ->setOwnershipDutyInfo($model->{UserInfo::OWNERSHIP_DUTY_INFO})
            ->setFraction($model->pivot?->fraction)
        ;

        if (isset($model->getRelations()[User::ACCOUNTS])) {
            $accounts = $model->getRelation(User::ACCOUNTS);
            $accountsCollection = new AccountCollection();
            foreach ($accounts as $account) {
                $accountsCollection->add(AccountLocator::AccountFactory()->makeDtoFromObject($account));
            }
            $result->setAccounts($accountsCollection);
        }

        if (isset($model->getRelations()[User::EX_DATA])) {
            $exData    = $model->getRelation(User::EX_DATA);
            $exDataDTO = $exData ? ExDataLocator::ExDataFactory()->makeDtoFromObject($exData) : null;

            $data = new UserExDataDTO($exDataDTO?->getData());
            $result->setExData($data)->getExData()?->setId($exData->id);
        }
        if ( ! $result->getExData()) {
            $result->setExData(new UserExDataDTO());
        }

        if (isset($model->getRelations()[User::ROLES])) {
            $role = $model->getRelation(User::ROLES)->first();
            $result->setRole($role ? RoleLocator::RoleFactory()->makeDtoFromObject($role) : null);
        }

        return $result;
    }
}
