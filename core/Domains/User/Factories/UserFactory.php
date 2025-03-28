<?php declare(strict_types=1);

namespace Core\Domains\User\Factories;

use App\Models\User;
use Core\Domains\Access\RoleLocator;
use Core\Domains\Account\AccountLocator;
use Core\Domains\User\Enums\UserIdEnum;
use Core\Domains\User\Models\UserDTO;
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

        return $result->fill([
            User::ID             => $dto->getId(),
            User::EMAIL          => $dto->getEmail(),
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
            ->setFirstName($model->first_name)
            ->setMiddleName($model->middle_name)
            ->setLastName($model->last_name)
            ->setCreatedAt($model->created_at)
            ->setUpdatedAt($model->updated_at)
        ;

        if (isset($model->getRelations()[User::ACCOUNTS])) {
            $account = $model->getRelation(User::ACCOUNTS)->first();
            $result->setAccount($account ? AccountLocator::AccountFactory()->makeDtoFromObject($account) : null);
        }

        if (isset($model->getRelations()[User::ROLES])) {
            $role = $model->getRelation(User::ROLES)->first();
            $result->setRole($role ? RoleLocator::RoleFactory()->makeDtoFromObject($role) : null);
        }

        return $result;
    }
}
