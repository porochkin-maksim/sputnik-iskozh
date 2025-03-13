<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Users;

use lc;
use App\Http\Resources\AbstractResource;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Domains\User\Models\UserDTO;
use Core\Responses\ResponsesEnum;

readonly class UserResource extends AbstractResource
{
    public function __construct(
        private UserDTO $user,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $access = lc::roleDecorator();

        return [
            'id'          => $this->user->getId(),
            'firstName'   => $this->user->getFirstName(),
            'middleName'  => $this->user->getMiddleName(),
            'lastName'    => $this->user->getLastName(),
            'email'       => $this->user->getEmail(),
            'roleId'      => (int) ($this->user->getRole()?->getId()),
            'roleName'    => ($this->user->getRole()?->getName()),
            'accountId'   => (int) ($this->user->getAccount()?->getId()),
            'accountName' => ($this->user->getAccount()?->getNumber()),
            'actions'     => [
                ResponsesEnum::VIEW => $access->can(PermissionEnum::USERS_VIEW),
                ResponsesEnum::EDIT => $access->can(PermissionEnum::USERS_EDIT),
                ResponsesEnum::DROP => $access->can(PermissionEnum::USERS_DROP),
            ],
            'historyUrl'  => HistoryChangesLocator::route(
                type     : HistoryType::USER,
                primaryId: $this->user->getId(),
            ),
        ];
    }
}