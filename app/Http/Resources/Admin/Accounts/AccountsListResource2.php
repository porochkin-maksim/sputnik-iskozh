<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Accounts;

use App\Http\Resources\AbstractResource;
use Core\Domains\Account\Collections\AccountCollection;

readonly class AccountsListResource2 extends AbstractResource
{
    public function __construct(
        private AccountCollection $accountCollection,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $result = [];

        foreach ($this->accountCollection as $account) {
            $result[] = new AccountResource($account);
        }

        return $result;
    }
}
