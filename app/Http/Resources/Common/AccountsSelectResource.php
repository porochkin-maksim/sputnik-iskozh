<?php declare(strict_types=1);

namespace App\Http\Resources\Common;

use App\Http\Resources\AbstractResource;
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