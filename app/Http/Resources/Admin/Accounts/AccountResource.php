<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Accounts;

use App\Http\Resources\AbstractResource;
use App\Http\Resources\Admin\Users\UserResource;
use App\Resources\RouteNames;
use App\Support\HistoryChangesRoute;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Account\AccountEntity;
use Core\Domains\HistoryChanges\HistoryType;
use Core\Shared\Helpers\DateTime\DateTimeFormat;
use lc;

readonly class AccountResource extends AbstractResource
{
    public function __construct(
        private AccountEntity $account,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $access = lc::roleDecorator();
        $isSnt  = $this->account->isSnt();

        $exData = $this->account->getExData();

        return [
            'id'              => $this->account->getId(),
            'number'          => $this->account->getNumber(),
            'size'            => $this->account->getSize(),
            'balance'         => $this->account->getBalance(),
            'isInvoicing'     => $this->account->isInvoicing(),
            'fraction'        => $this->account->getFraction(),
            'ownerDate'       => $this->account->getOwnerDate()?->format(DateTimeFormat::DATE_DEFAULT),
            'fractionPercent' => $this->account->getFractionPercent(),

            'cadastreNumber' => $exData->getCadastreNumber(),

            'actions'    => [
                'view' => $access->can(PermissionEnum::ACCOUNTS_VIEW),
                'edit' => ! $isSnt && $access->can(PermissionEnum::ACCOUNTS_EDIT),
                'drop' => ! $isSnt && $access->can(PermissionEnum::ACCOUNTS_DROP),
                'counters'          => [
                    'view' => $access->can(PermissionEnum::COUNTERS_VIEW),
                    'edit' => $access->can(PermissionEnum::COUNTERS_EDIT),
                    'drop' => $access->can(PermissionEnum::COUNTERS_DROP),
                ],
            ],
            'historyUrl' => $this->account->getId()
                ? HistoryChangesRoute::make(
                    type     : HistoryType::ACCOUNT,
                    primaryId: $this->account->getId(),
                ) : null,
            'viewUrl'    => $this->getViewUrl(),
            'users'      => $this->account->getUsers() ? array_map(static fn($user) => new UserResource($user), $this->account->getUsers()->toArray()) : [],
        ];
    }

    public function getViewUrl(): ?string
    {
        return $this->account->getId() ? route(RouteNames::ADMIN_ACCOUNT_VIEW, ['accountId' => $this->account->getId()]) : null;
    }
}
