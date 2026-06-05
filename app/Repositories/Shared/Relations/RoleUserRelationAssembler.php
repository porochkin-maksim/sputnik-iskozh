<?php declare(strict_types=1);

namespace App\Repositories\Shared\Relations;

use App\Models\User as UserModel;
use Core\Domains\User\UserCollection;
use Core\Domains\User\UserEntity;
use Core\Domains\User\UserFactory;
use Core\Shared\Helpers\DateTime\DateTimeHelper;

readonly class RoleUserRelationAssembler
{
    public function __construct(
        private UserFactory $userFactory,
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
}
