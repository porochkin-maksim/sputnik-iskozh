<?php declare(strict_types=1);

namespace Core\App\Billing\Invoice;

use App\Models\Billing\Period;
use App\Services\Money\MoneyService;
use Core\Domains\Account\AccountIdEnum;
use Core\Domains\Account\AccountService;
use Core\Domains\Billing\Claim\ClaimCollection;
use Core\Domains\Billing\Claim\ClaimFactory;
use Core\Domains\Billing\Claim\ClaimService;
use Core\Domains\Billing\Debt\DebtMigrationService;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Invoice\InvoiceTypeEnum;
use Core\Domains\Billing\Service\ServiceCatalogService;
use Core\Domains\Billing\Service\ServiceTypeEnum;
use Illuminate\Support\Str;
use RuntimeException;

readonly class CreateClaimsAndPaymentsForRegularInvoiceCommand
{
    public function __construct(
        private InvoiceService          $invoiceService,
        private ClaimService            $claimService,
        private ClaimFactory            $claimFactory,
        private AccountService          $accountService,
        private ServiceCatalogService   $serviceService,
        private DebtMigrationService    $debtMigrationService,
        private RecalcClaimsPaidCommand $claimsPaidCommand,
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

        $oldDebts = $this->getMigratingClaimsToNewPeriod($invoice);

        $newPeriodServices = $this->serviceService->getByPeriodId($invoice->getPeriodId());
        $newDebtService    = $newPeriodServices->getByType(ServiceTypeEnum::DEBT)->first();

        $newClaims = new ClaimCollection();

        if ($newDebtService && ! $oldDebts->isEmpty()) {
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

                $newClaims->add($this->claimService->save($claim));
            }
        }

        foreach ($newPeriodServices as $service) {
            if ( ! in_array($service->getType(), [
                ServiceTypeEnum::MEMBERSHIP_FEE,
                ServiceTypeEnum::TARGET_FEE,
                ServiceTypeEnum::PERSONAL_FEE,
            ], true)) {
                continue;
            }

            $cost   = null;
            $tariff = MoneyService::parse($service->getCost());
            if ($service->getType() === ServiceTypeEnum::MEMBERSHIP_FEE) {
                $size = (int) $this->accountService->getById($invoice->getAccountId())?->getSize();
                $cost = $tariff->multiply($size);
            }
            else {
                $cost = MoneyService::parse($service->getCost());
                $size = MoneyService::toInt($cost->divide(MoneyService::toFloat($tariff)));
            }

            $claim = $this->claimFactory->makeDefault()
                ->setInvoiceId($invoice->getId())
                ->setServiceId($service->getId())
                ->setTariff($service->getCost())
                ->setCost(MoneyService::toFloat($cost))
                ->setQuantity($service->getType() === ServiceTypeEnum::MEMBERSHIP_FEE ? $size : 1)
            ;

            $newClaims->add($this->claimService->save($claim));
        }

        $this->claimsPaidCommand->execute($invoice->getId());
    }

    private function getMigratingClaimsToNewPeriod(InvoiceEntity $invoice): ClaimCollection
    {
        return $this->debtMigrationService->getUnmigratedOldDebts($invoice);
    }
}
