<?php declare(strict_types=1);

namespace App\Http\Resources\Profile\Accounts;

use App\Http\Resources\AbstractResource;
use Core\Domains\Account\Models\AccountDTO;
use Core\Enums\DateTimeFormat;

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
            // 'id'              => $this->account->getId(),
            'number'          => $this->account->getNumber(),
            'size'            => $this->account->getSize(),
            // 'primary_user_id' => $this->account->getPrimaryUserId(),
            // 'is_member'       => $this->account->isMember(),
            // 'is_manager'      => $this->account->isManager(),
            // 'updatedAt'       => $this->account->getUpdatedAt()?->format(DateTimeFormat::DATE_TIME_VIEW_FORMAT),
        ];
    }
}
