<?php declare(strict_types=1);

namespace Core\Objects\User\Factories;

use App\Models\User;
use Core\Objects\User\Models\UserDTO;
use Illuminate\Support\Facades\Hash;

class UserFactory
{
    public function makeFromDto(UserDTO $dto): User
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

    private function encryptPassword(?string $password): ?string
    {
        return Hash::make($password);
    }
}
