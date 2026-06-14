<?php declare(strict_types=1);

namespace Core\Domains\User\Events;

use Core\Domains\Infra\DbLock\Enum\LockNameEnum;

readonly class ImportUsersSaveRequested
{
    /**
     * @param array[] $users  массив данных из JSON-сериализации UserImportItem (см. UserImportItem::jsonSerialize)
     */
    public function __construct(
        public array        $users,
        public LockNameEnum $lockName,
    )
    {
    }
}
