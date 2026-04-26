<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Users;

use lc;
use App\Http\Resources\AbstractResource;
use App\Support\HistoryChangesRoute;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\HistoryChanges\HistoryType;
use Core\Domains\User\UserCollection;

/**
 * @deprecated
 */
readonly class UsersListResource extends AbstractResource
{
    public function __construct(
        private UserCollection $userCollection,
        private int            $totalUsersCount,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $access = lc::roleDecorator();
        $result = [
            'users'      => [],
            'total'      => $this->totalUsersCount,
            'actions'    => [
                'view' => $access->can(PermissionEnum::USERS_VIEW),
                'edit' => $access->can(PermissionEnum::USERS_EDIT),
                'drop' => $access->can(PermissionEnum::USERS_DROP),
            ],
            'historyUrl' => HistoryChangesRoute::make(
                type: HistoryType::USER,
            ),
        ];

        foreach ($this->userCollection as $user) {
            $result['users'][] = new UserResource($user);
        }

        return $result;
    }
}
