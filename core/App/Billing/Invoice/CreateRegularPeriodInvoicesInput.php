<?php declare(strict_types=1);

namespace Core\App\Billing\Invoice;

readonly class CreateRegularPeriodInvoicesInput
{
    public function __construct(
        public int   $periodId,
        public array $accountIds = [],
    )
    {
    }
}
