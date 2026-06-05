<?php declare(strict_types=1);

namespace Core\Domains\Billing\Events;

readonly class RegularPeriodInvoiceBatchRequested
{
    public function __construct(
        public int   $periodId,
        public array $accountIds,
    )
    {
    }
}
