<?php declare(strict_types=1);

namespace Core\Objects\User\Factories;

use App\Models\User;
use Core\Objects\User\Models\UserDTO;
use Illuminate\Support\Facades\Hash;

class UserFactory
{
    private function encryptPassword(?string $password): ?string
    {
        return Hash::make($password);
    }

    public function makeModelFromDto(UserDTO $dto): User
    {
        $result = new User([
            User::ID             => $dto->getId(),
            User::EMAIL          => $dto->getEmail(),
            User::REMEMBER_TOKEN => $dto->getRememberToken(),
        ]);

        if ($dto->getPassword()) {
            $result->setAttribute(User::PASSWORD, $this->encryptPassword($dto->getPassword()));
        }

        return $result;
    }

    public function makeDtoFromObject(?User $user): UserDTO
    {
        $result = new UserDTO();

        $result
            ->setId($user->id)
            ->setEmail($user->email)
            ->setFirstName($user->first_name)
            ->setMiddleName($user->middle_name)
            ->setLastName($user->last_name);

        return $result;
    }
}
