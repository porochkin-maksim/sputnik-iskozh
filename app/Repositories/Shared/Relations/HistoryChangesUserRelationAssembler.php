<?php declare(strict_types=1);

namespace App\Repositories\Shared\Relations;

use App\Models\User as UserModel;
use Core\Domains\User\UserEntity;
use Core\Domains\User\UserFactory;
use Core\Domains\User\UserIdEnum;
use Core\Shared\Helpers\DateTime\DateTimeHelper;

readonly class HistoryChangesUserRelationAssembler
{
    public function __construct(
        private UserFactory $userFactory,
    )
    {
    }

    public function makeUser(?UserModel $data, int|string|null $userId = null): UserEntity
    {
        if ($data) {
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

        return match ($userId) {
            UserIdEnum::ROBOT => $this->userFactory->makeRobot(),
            default => $this->userFactory->makeUndefined(),
        };
    }
}
