<?php declare(strict_types=1);

namespace Core\Domains\User\Factories;

use App\Models\User;
use Core\Domains\Access\Factories\RoleFactory;
use Core\Domains\Account\Factories\AccountFactory;
use Core\Domains\User\Models\UserDTO;
use Illuminate\Support\Facades\Hash;

readonly class UserFactory
{
    public function __construct(
        public AccountFactory $accountFactory,
        public RoleFactory    $roleFactory,
    )
    {
    }

    private function encryptPassword(?string $password): ?string
    {
        return Hash::make($password);
    }

    public function makeModelFromDto(UserDTO $dto, ?User $user): User
    {
        if ($user) {
            $result = $user;
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

    public function makeDtoFromObject(?User $user): UserDTO
    {
        $result = new UserDTO($user);

        $result
            ->setId($user->id)
            ->setEmail($user->email)
            ->setFirstName($user->first_name)
            ->setMiddleName($user->middle_name)
            ->setLastName($user->last_name);

        if (isset($user->getRelations()[User::ACCOUNTS])) {
            $account = $user->getRelation(User::ACCOUNTS)->first();
            if ($account) {
                $result->setAccount($this->accountFactory->makeDtoFromObject($account));
            }
        }

        $role = $this->roleFactory->makeForUserId($user->id);

        if ($role) {
            $result->setRole($role);
        }
        elseif (isset($user->getRelations()[User::ROLES])) {
            $role = $user->getRelation(User::ROLES)->first();
            $result->setRole($role ? $this->roleFactory->makeDtoFromObject($role) : null);
        }

        return $result;
    }

    public function makeUndefined(): UserDTO
    {
        return new UserDTO();
    }
}
