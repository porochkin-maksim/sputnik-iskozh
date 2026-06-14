<?php declare(strict_types=1);

namespace Core\Domains\Account\Events;

use Core\Domains\Infra\DbLock\Enum\LockNameEnum;

readonly class ImportAccountsSaveRequested
{
    /**
     * @param array[] $accounts  массив данных из JSON-сериализации AccountImportItem (см. AccountImportItem::jsonSerialize)
     */
    public function __construct(
        public array        $accounts,
        public LockNameEnum $lockName,
    )
    {
    }
}
