<?php declare(strict_types=1);

namespace Core\App\Billing\Invoice;

use Core\Domains\Account\AccountIdEnum;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Invoice\InvoiceTypeEnum;
use Core\Domains\Billing\Period\PeriodService;
use RuntimeException;

readonly class CreateClaimsAndPaymentsForRegularInvoiceCommand
{
    public function __construct(
        private InvoiceService                       $invoiceService,
        private CreateClaimsForRegularInvoiceCommand $createClaimsCommand,
        private MigrateDebtsForRegularInvoiceCommand $migrateDebtsCommand,
        private RecalcClaimsPaidCommand              $claimsPaidCommand,
        private PeriodService                        $periodService,
    )
    {
    }

    public function execute(int $invoiceId): void
    {
        $invoice = $this->invoiceService->getById($invoiceId);
        if ($invoice === null) {
            throw new RuntimeException("Счёт не найден #{$invoiceId}");
        }

        if ($invoice->getType() !== InvoiceTypeEnum::REGULAR) {
            return;
        }

        if ($invoice->getAccountId() === AccountIdEnum::SNT->value) {
            return;
        }

        $openPeriods = $this->periodService->getOpenPeriods();
        if ($openPeriods->count() <= 1) {
            $this->migrateDebtsCommand->execute($invoiceId);
        }

        $this->createClaimsCommand->execute($invoiceId);
        $this->claimsPaidCommand->execute($invoice->getId());
    }
}
