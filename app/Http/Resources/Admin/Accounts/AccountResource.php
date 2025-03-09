<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Accounts;

use App\Http\Resources\AbstractResource;
use Core\Domains\Account\Models\AccountDTO;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;

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
            'id'         => $this->account->getId(),
            'number'     => $this->account->getNumber(),
            'size'       => $this->account->getSize(),
            'balance'    => $this->account->getBalance(),
            'is_member'  => $this->account->isMember(),
            'actions'    => [
                'drop' => ! $this->account->isVerified(),
            ],
            'historyUrl' => $this->account->getId()
                ? HistoryChangesLocator::route(
                    type: HistoryType::ACCOUNT,
                    primaryId: $this->account->getId(),
                ) : null,
        ];
    }
}
