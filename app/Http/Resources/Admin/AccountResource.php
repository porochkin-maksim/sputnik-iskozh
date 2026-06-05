<?php declare(strict_types=1);

namespace App\Http\Resources\Admin;

use App\Http\Resources\AbstractResource;
use App\Http\Resources\Admin\Users\UserResource;
use App\Http\Resources\Shared\ResourseList;
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

            'historyUrl' => $this->account->getId()
                ? HistoryChangesRoute::make(
                    type     : HistoryType::ACCOUNT,
                    primaryId: $this->account->getId(),
                ) : null,
            'viewUrl'    => $this->getViewUrl(),
            'actions'    => $this->getActions(),
            'users'      => $this->account->getUsers() ? new ResourseList($this->account->getUsers(), UserResource::class) : [],
        ];
    }

    public function getViewUrl(): ?string
    {
        return $this->account->getId() ? route(RouteNames::ADMIN_ACCOUNT_VIEW, ['accountId' => $this->account->getId()]) : null;
    }

    /**
     * @return array<string, bool>
     */
    private function getActions(): array
    {
        $access  = lc::roleDecorator();
        $account = $this->account;

        return [
            'view' => $access->can(PermissionEnum::ACCOUNTS_VIEW),
            'edit' => ! $account->isSnt() && $access->can(PermissionEnum::ACCOUNTS_EDIT),
            'drop' => ! $account->isSnt() && $access->can(PermissionEnum::ACCOUNTS_DROP),
        ];
    }
}
