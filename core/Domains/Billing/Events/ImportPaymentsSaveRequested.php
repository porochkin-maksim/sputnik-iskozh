<?php declare(strict_types=1);

namespace Core\Domains\Billing\Events;

use Core\Domains\Infra\DbLock\Enum\LockNameEnum;

readonly class ImportPaymentsSaveRequested
{
    public function __construct(
        public array        $paymentsData,
        public LockNameEnum $lockName,
    )
    {
    }
}
