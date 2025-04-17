<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Accounts;

use App\Http\Resources\Admin\Users\UserResource;
use Core\Enums\DateTimeFormat;
use Core\Resources\RouteNames;
use lc;
use App\Http\Resources\AbstractResource;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Account\Models\AccountDTO;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Responses\ResponsesEnum;

readonly class AccountResource extends AbstractResource
{
    public function __construct(
        private AccountDTO $account,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $access = lc::roleDecorator();
        $isSnt  = $this->account->isSnt();

        $exData = $this->account->getExData();

        return [
            'id'          => $this->account->getId(),
            'number'      => $this->account->getNumber(),
            'size'        => $this->account->getSize(),
            'balance'     => $this->account->getBalance(),
            'isInvoicing' => $this->account->isInvoicing(),

            'cadastreNumber' => $exData->getCadastreNumber(),
            'registryDate'   => $exData->getRegistryDate()?->format(DateTimeFormat::DATE_DEFAULT),

            'actions'    => [
                ResponsesEnum::VIEW => $access->can(PermissionEnum::ACCOUNTS_VIEW),
                ResponsesEnum::EDIT => ! $isSnt && $access->can(PermissionEnum::ACCOUNTS_EDIT),
                ResponsesEnum::DROP => ! $isSnt && $access->can(PermissionEnum::ACCOUNTS_DROP),
                'counters'          => [
                    ResponsesEnum::VIEW => $access->can(PermissionEnum::COUNTERS_VIEW),
                    ResponsesEnum::EDIT => $access->can(PermissionEnum::COUNTERS_EDIT),
                    ResponsesEnum::DROP => $access->can(PermissionEnum::COUNTERS_DROP),
                ],
            ],
            'historyUrl' => $this->account->getId()
                ? HistoryChangesLocator::route(
                    type     : HistoryType::ACCOUNT,
                    primaryId: $this->account->getId(),
                ) : null,
            'viewUrl'    => $this->account->getId() ? route(RouteNames::ADMIN_ACCOUNT_VIEW, ['accountId' => $this->account->getId()]) : null,
            'users'      => $this->account->getUsers() ? array_map(static fn($user) => new UserResource($user), $this->account->getUsers()->toArray()) : [],
        ];
    }
}
