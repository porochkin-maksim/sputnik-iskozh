<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Users;

use App\Http\Resources\Admin\Accounts\AccountResource;
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

        $user    = $this->user;
        $canEdit = UserIdEnum::OWNER !== $user->getId() || lc::isSuperAdmin();
        $exData  = $user->getExData();

        $curAccount = $user->getAccounts()?->getById((int) $user->getAccountId());

        $result = [
            'id'              => $user->getId(),
            'fullName'        => UserLocator::UserDecorator($user)->getFullName(),
            'firstName'       => $user->getFirstName(),
            'middleName'      => $user->getMiddleName(),
            'lastName'        => $user->getLastName(),
            'email'           => $user->getEmail(),
            'phone'           => $user->getPhone() ? PhoneHelper::normalizePhone($user->getPhone()) : null,
            'roleId'          => (int) ($user->getRole()?->getId()),
            'roleName'        => $user->getRole()?->getName(),
            'accountId'       => (int) ($user->getAccount()?->getId()),
            'fraction'        => $user->getFraction(),
            'fractionPercent' => $user->getFractionpercent(),
            'accountName'     => $curAccount?->getNumber(),
            'accountIds'      => $user->getAccountIds(),
            'emailVerifiedAt' => $user->getEmailVerifiedAt()?->format(DateTimeFormat::DATE_DEFAULT),
            'isDeleted'       => $user->isDeleted(),

            'ownershipDate'     => $user->getOwnershipDate()?->format(DateTimeFormat::DATE_DEFAULT),
            'ownershipDutyInfo' => $user->getOwnershipDutyInfo(),

            'addPhone'     => $exData->getPhone(),
            'legalAddress' => $exData->getLegalAddress(),
            'postAddress'  => $exData->getPostAddress(),
            'additional'   => $exData->getAdditional(),

            'actions'    => [
                ResponsesEnum::VIEW => $access->can(PermissionEnum::USERS_VIEW),
                ResponsesEnum::EDIT => !$user->isDeleted() && $canEdit && $access->can(PermissionEnum::USERS_EDIT),
                ResponsesEnum::DROP => $canEdit && $access->can(PermissionEnum::USERS_DROP),
                'account'           => [
                    ResponsesEnum::VIEW => $access->can(PermissionEnum::ACCOUNTS_VIEW),
                ],
            ],
            'historyUrl' => HistoryChangesLocator::route(
                type     : HistoryType::USER,
                primaryId: $user->getId(),
            ),
            'viewUrl'    => $user->getId() ? route(RouteNames::ADMIN_USER_VIEW, ['id' => $user->getId()]) : null,
        ];

        if ($curAccount && $access->can(PermissionEnum::ACCOUNTS_VIEW)) {
            $result['account'] = new AccountResource($curAccount);
        }

        if ($user->getAccounts() && $access->can(PermissionEnum::ACCOUNTS_VIEW)) {
            $result['accounts'] = [];
            foreach ($user->getAccounts() as $account) {
                $result['accounts'][] = new AccountResource($account);
            }
        }

        return $result;
    }
}