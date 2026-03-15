<?php declare(strict_types=1);

namespace App\Http\Resources\Profile\Accounts;

use App\Http\Resources\AbstractResource;
use Core\Domains\Account\Models\AccountDTO;

readonly class AccountResource extends AbstractResource
{
    public function __construct(
        private AccountDTO $account,
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'number' => $this->account->getNumber(),
            'size'   => $this->account->getSize(),
        ];
    }
}
