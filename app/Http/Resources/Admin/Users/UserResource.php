<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Users;

use App\Http\Resources\Admin\Accounts\AccountResource;
use Core\Domains\Account\Models\AccountDTO;
use Core\Domains\User\Enums\UserIdEnum;
use Core\Domains\User\UserLocator;
use Core\Enums\DateTimeFormat;
use Core\Helpers\Phone\PhoneHelper;
use Core\Resources\RouteNames;
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

        $canEdit = UserIdEnum::OWNER !== $this->user->getId() || lc::isSuperAdmin();
        $exData  = $this->user->getExData();

        $curAccount = $this->user->getAccounts()?->getById((int) $this->user->getAccountId());

        $result = [
            'id'              => $this->user->getId(),
            'fullName'        => UserLocator::UserDecorator($this->user)->getFullName(),
            'firstName'       => $this->user->getFirstName(),
            'middleName'      => $this->user->getMiddleName(),
            'lastName'        => $this->user->getLastName(),
            'email'           => $this->user->getEmail(),
            'phone'           => $this->user->getPhone() ? PhoneHelper::normalizePhone($this->user->getPhone()) : null,
            'roleId'          => (int) ($this->user->getRole()?->getId()),
            'roleName'        => $this->user->getRole()?->getName(),
            'accountId'       => (int) ($this->user->getAccount()?->getId()),
            'fraction'        => $this->user->getFraction(),
            'fractionPercent' => $this->user->getFractionpercent(),
            'accountName'     => $curAccount?->getNumber(),
            'accountIds'      => $this->user->getAccountIds(),
            'emailVerifiedAt' => $this->user->getEmailVerifiedAt()?->format(DateTimeFormat::DATE_DEFAULT),

            'ownershipDate'     => $this->user->getOwnershipDate()?->format(DateTimeFormat::DATE_DEFAULT),
            'ownershipDutyInfo' => $this->user->getOwnershipDutyInfo(),

            'addPhone'     => $exData->getPhone(),
            'legalAddress' => $exData->getLegalAddress(),
            'postAddress'  => $exData->getPostAddress(),
            'additional'   => $exData->getAdditional(),

            'actions'    => [
                ResponsesEnum::VIEW => $access->can(PermissionEnum::USERS_VIEW),
                ResponsesEnum::EDIT => $canEdit && $access->can(PermissionEnum::USERS_EDIT),
                ResponsesEnum::DROP => $canEdit && $access->can(PermissionEnum::USERS_DROP),
                'account'           => [
                    ResponsesEnum::VIEW => $access->can(PermissionEnum::ACCOUNTS_VIEW),
                ],
            ],
            'historyUrl' => HistoryChangesLocator::route(
                type     : HistoryType::USER,
                primaryId: $this->user->getId(),
            ),
            'viewUrl'    => $this->user->getId() ? route(RouteNames::ADMIN_USER_VIEW, ['id' => $this->user->getId()]) : null,
        ];

        if ($curAccount && $access->can(PermissionEnum::ACCOUNTS_VIEW)) {
            $result['account'] = new AccountResource($curAccount);
        }

        if ($this->user->getAccounts() && $access->can(PermissionEnum::ACCOUNTS_VIEW)) {
            $result['accounts'] = [];
            foreach ($this->user->getAccounts() as $account) {
                $result['accounts'][] = new AccountResource($account);
            }
        }

        return $result;
    }
}