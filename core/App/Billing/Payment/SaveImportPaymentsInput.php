<?php declare(strict_types=1);

namespace Core\App\Billing\Payment;

use Core\Domains\Billing\Events\ImportPaymentData;

readonly class SaveImportPaymentsInput
{
    /**
     * @param ImportPaymentData[] $paymentsData
     */
    public function __construct(
        public array $paymentsData,
    )
    {
    }
}
