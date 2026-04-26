<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Users;

use App\Http\Resources\AbstractResource;
use App\Http\Resources\Admin\Accounts\AccountResource;
use App\Resources\RouteNames;
use App\Support\HistoryChangesRoute;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\HistoryChanges\HistoryType;
use Core\Domains\User\UserEntity;
use Core\Domains\User\UserIdEnum;
use Core\Shared\Helpers\DateTime\DateTimeFormat;
use Core\Shared\Helpers\Phone\PhoneHelper;
use lc;

readonly class UserResource extends AbstractResource
{
    public function __construct(
        private UserEntity $user,
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
            'fullName'        => $user->getViewer()->getFullName(),
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
            'ownerDate'       => $user->getOwnerDate()?->format(DateTimeFormat::DATE_DEFAULT),
            'accountName'     => $curAccount?->getNumber(),
            'accountIds'      => $user->getAccountIds(),
            'emailVerifiedAt' => $user->getEmailVerifiedAt()?->format(DateTimeFormat::DATE_DEFAULT),
            'isRealEmail'     => $user->isRealEmail(),
            'isDeleted'       => $user->isDeleted(),

            'membershipDate'     => $user->getMembershipDate()?->format(DateTimeFormat::DATE_DEFAULT),
            'membershipDutyInfo' => $user->getMembershipDutyInfo(),

            'addPhone'     => $exData->getPhone(),
            'legalAddress' => $exData->getLegalAddress(),
            'postAddress'  => $exData->getPostAddress(),
            'additional'   => $exData->getAdditional(),

            'actions'    => [
                'view' => $access->can(PermissionEnum::USERS_VIEW),
                'edit' => ! $user->isDeleted() && $canEdit && $access->can(PermissionEnum::USERS_EDIT),
                'drop' => $canEdit && $access->can(PermissionEnum::USERS_DROP),
                'account'           => [
                    'view' => $access->can(PermissionEnum::ACCOUNTS_VIEW),
                ],
            ],
            'historyUrl' => HistoryChangesRoute::make(
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
