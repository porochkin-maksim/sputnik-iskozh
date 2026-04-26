<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Accounts;

use App\Http\Resources\AbstractResource;
use App\Http\Resources\Common\AccountsSelectResource;
use App\Support\HistoryChangesRoute;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Account\AccountCollection;
use Core\Domains\HistoryChanges\HistoryType;
use lc;

/**
 * @deprecated
 */
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
            'allAccounts' => new AccountsSelectResource($this->allAccountCollection, false),
            'total'       => $this->totalAccountsCount,
            'actions'    => [
                'view' => $access->can(PermissionEnum::ACCOUNTS_VIEW),
                'edit' => $access->can(PermissionEnum::ACCOUNTS_EDIT),
                'drop' => $access->can(PermissionEnum::ACCOUNTS_DROP),
            ],
            'historyUrl'  => HistoryChangesRoute::make(
                type: HistoryType::ACCOUNT,
            ),
        ];

        foreach ($this->accountCollection as $account) {
            $result['accounts'][] = new AccountResource($account);
        }

        return $result;
    }
}
