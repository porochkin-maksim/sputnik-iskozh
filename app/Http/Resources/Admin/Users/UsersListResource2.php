<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Users;

use App\Http\Resources\AbstractResource;

use Core\Domains\User\Collections\UserCollection;

readonly class UsersListResource2 extends AbstractResource
{
    public function __construct(
        private UserCollection $userCollection,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $result = [];

        foreach ($this->userCollection as $user) {
            $result[] = new UserResource($user);
        }

        return $result;
    }
}