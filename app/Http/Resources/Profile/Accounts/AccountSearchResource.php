<?php declare(strict_types=1);

namespace App\Http\Resources\Profile\Accounts;

use App\Http\Resources\AbstractResource;
use Core\Domains\Account\AccountCollection;

readonly class AccountSearchResource extends AbstractResource
{
    public function __construct(
        private AccountCollection $accounts,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $result = [];

        foreach ($this->accounts as $account) {
            $result[] = [
                'id'     => $account->getId(),
                'number' => $account->getNumber(),
                'size'   => $account->getSize(),
            ];
        }

        return $result;
    }
}
