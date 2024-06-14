<?php declare(strict_types=1);

namespace Core\Domains\Account\Models;

use Core\Enums\DateTimeFormat;

readonly class Dossier implements \JsonSerializable
{
    public function __construct(
        private AccountDTO $account
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'updatedAt' => $this->account->getUpdatedAt()?->format(DateTimeFormat::DATE_TIME_VIEW_FORMAT),
        ];
    }
}
