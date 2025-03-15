<?php declare(strict_types=1);

namespace App\Http\Resources\Profile\Users;

use App\Http\Resources\AbstractResource;
use Core\Domains\User\Models\UserDTO;

readonly class UserResource extends AbstractResource
{
    public function __construct(
        private UserDTO $user,
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'firstName'  => $this->user->getFirstName(),
            'middleName' => $this->user->getMiddleName(),
            'lastName'   => $this->user->getLastName(),
            'email'      => $this->user->getEmail(),
        ];
    }
}