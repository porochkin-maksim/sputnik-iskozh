<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Users;

use app;
use App\Http\Resources\AbstractResource;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Domains\User\Collections\UserCollection;
use Core\Responses\ResponsesEnum;

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
        $access = app::roleDecorator();
        $result = [
            'users'      => [],
            'total'      => $this->totalUsersCount,
            'actions'    => [
                ResponsesEnum::VIEW => $access->can(PermissionEnum::USERS_VIEW),
                ResponsesEnum::EDIT => $access->can(PermissionEnum::USERS_EDIT),
                ResponsesEnum::DROP => $access->can(PermissionEnum::USERS_DROP),
            ],
            'historyUrl' => HistoryChangesLocator::route(
                type: HistoryType::USER,
            ),
        ];

        foreach ($this->userCollection as $user) {
            $result['users'][] = new UserResource($user);
        }

        return $result;
    }
}