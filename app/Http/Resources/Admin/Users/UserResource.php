<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Users;

use App\Http\Resources\AbstractResource;
use App\Http\Resources\Admin\AccountResource;
use App\Http\Resources\Shared\ResourseList;
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
        $user   = $this->user;
        $access = lc::roleDecorator();
        $canEdit = $access->can(PermissionEnum::USERS_EDIT)
            && (UserIdEnum::OWNER !== $user->getId() || lc::isSuperAdmin());
        $exData = $user->getExData();

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

            'historyUrl' => HistoryChangesRoute::make(
                type     : HistoryType::USER,
                primaryId: $user->getId(),
            ),
            'viewUrl'    => $user->getId() ? route(RouteNames::ADMIN_USER_VIEW, ['id' => $user->getId()]) : null,
            'actions'    => [
                'view' => $access->can(PermissionEnum::USERS_VIEW),
                'edit' => $canEdit,
                'drop' => $access->can(PermissionEnum::USERS_DROP) && UserIdEnum::OWNER !== $user->getId(),
            ],
        ];

        if ($curAccount) {
            $result['account'] = new AccountResource($curAccount);
        }

        if ($user->getAccounts()) {
            $result['accounts'] = new ResourseList($user->getAccounts(), AccountResource::class);
        }

        return $result;
    }
}
