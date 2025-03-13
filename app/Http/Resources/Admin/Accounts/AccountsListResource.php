<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Accounts;

use lc;
use App\Http\Resources\AbstractResource;
use App\Http\Resources\Common\SelectOptionResource;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Account\Collections\AccountCollection;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Responses\ResponsesEnum;

readonly class AccountsListResource extends AbstractResource
{
    public function __construct(
        private AccountCollection $accountCollection,
        private int               $totalAccountsCount,
        private AccountCollection $allAccountCollection,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $access = lc::roleDecorator();
        $result = [
            'accounts'    => [],
            'allAccounts' => [],
            'total'       => $this->totalAccountsCount,
            'actions'    => [
                ResponsesEnum::VIEW => $access->can(PermissionEnum::ACCOUNTS_VIEW),
                ResponsesEnum::EDIT => $access->can(PermissionEnum::ACCOUNTS_EDIT),
                ResponsesEnum::DROP => $access->can(PermissionEnum::ACCOUNTS_DROP),
            ],
            'historyUrl'  => HistoryChangesLocator::route(
                type: HistoryType::ACCOUNT,
            ),
        ];

        foreach ($this->accountCollection as $account) {
            $result['accounts'][] = new AccountResource($account);
        }

        foreach ($this->allAccountCollection as $account) {
            $result['allAccounts'][] = new SelectOptionResource(
                $account->getId(),
                $account->getNumber(),
            );
        }

        return $result;
    }
}
