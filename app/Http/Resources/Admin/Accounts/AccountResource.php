<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Accounts;

use app;
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
        $access = app::roleDecorator();
        return [
            'id'         => $this->account->getId(),
            'number'     => $this->account->getNumber(),
            'size'       => $this->account->getSize(),
            'balance'    => $this->account->getBalance(),
            'is_member'  => $this->account->isMember(),
            'actions'    => [
                ResponsesEnum::VIEW => $access->can(PermissionEnum::ACCOUNTS_VIEW),
                ResponsesEnum::EDIT => $access->can(PermissionEnum::ACCOUNTS_EDIT),
                ResponsesEnum::DROP => $access->can(PermissionEnum::ACCOUNTS_DROP),
            ],
            'historyUrl' => $this->account->getId()
                ? HistoryChangesLocator::route(
                    type: HistoryType::ACCOUNT,
                    primaryId: $this->account->getId(),
                ) : null,
        ];
    }
}
