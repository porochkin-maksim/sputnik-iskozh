<?php declare(strict_types=1);

namespace Core\App\Billing\Invoice;

use Core\Domains\Account\AccountIdEnum;
use Core\Domains\Billing\Claim\ClaimFactory;
use Core\Domains\Billing\Claim\ClaimService;
use Core\Domains\Billing\Debt\DebtMigrationService;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Invoice\InvoiceTypeEnum;
use Core\Domains\Billing\Service\ServiceCatalogService;
use Core\Domains\Billing\Service\ServiceTypeEnum;
use Illuminate\Support\Str;
use RuntimeException;

readonly class MigrateDebtsForRegularInvoiceCommand
{
    public function __construct(
        private InvoiceService        $invoiceService,
        private ClaimService          $claimService,
        private ClaimFactory          $claimFactory,
        private ServiceCatalogService $serviceService,
        private DebtMigrationService  $debtMigrationService,
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

        $oldDebts = $this->debtMigrationService->getUnmigratedOldDebts($invoice);

        $newPeriodServices = $this->serviceService->getByPeriodId($invoice->getPeriodId());
        $newDebtService    = $newPeriodServices->getByType(ServiceTypeEnum::DEBT)->first();

        if ($newDebtService === null || $oldDebts->isEmpty()) {
            return;
        }

        foreach ($oldDebts as $oldDebtClaim) {
            $oldService     = $oldDebtClaim->getService();
            $newServiceName = Str::contains($oldService?->getName(), '(долг за период')
                ? $oldService?->getName()
                : sprintf(
                    '%s (долг за период %s)',
                    $oldService?->getName() ? : $oldService?->getType()?->name(),
                    $oldDebtClaim->getInvoice()?->getPeriod()?->getName(),
                );

            $claim = $this->claimFactory->makeDefault()
                ->setQuantity($oldDebtClaim->getDelta() / $oldDebtClaim->getTariff())
                ->setInvoiceId($invoice->getId())
                ->setServiceId($newDebtService->getId())
                ->setOriginalServiceId($oldDebtClaim->getServiceId())
                ->setOriginalClaimId($oldDebtClaim->getId())
                ->setTariff($oldDebtClaim->getTariff())
                ->setCost($oldDebtClaim->getDelta())
                ->setName($newServiceName)
            ;

            $this->claimService->save($claim);
        }
    }
}
