<?php declare(strict_types=1);

namespace App\Http\Resources\Profile\Accounts;

use App\Http\Resources\AbstractResource;
use Core\Domains\Account\AccountEntity;

readonly class AccountResource extends AbstractResource
{
    public function __construct(
        private AccountEntity $account,
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'id'     => $this->account->getId(),
            'number' => $this->account->getNumber(),
            'size'   => $this->account->getSize(),
        ];
    }
}
