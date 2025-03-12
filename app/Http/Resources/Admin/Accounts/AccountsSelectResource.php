<?php

namespace App\Http\Resources\Admin\Accounts;

use App\Http\Resources\AbstractResource;
use App\Http\Resources\Common\SelectOptionResource;
use Core\Domains\Account\Collections\AccountCollection;

readonly class AccountsSelectResource extends AbstractResource
{
    public function __construct(
        private AccountCollection $accountCollection,
        private bool              $addEmptyOption,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $result = [];

        if ($this->addEmptyOption) {
            $result[] = new SelectOptionResource(0, 'Без участка');
        }
        foreach ($this->accountCollection as $account) {
            $result[] = new SelectOptionResource($account->getId(), $account->getNumber());
        }

        return $result;
    }
}