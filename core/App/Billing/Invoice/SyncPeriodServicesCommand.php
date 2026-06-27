<?php declare(strict_types=1);

namespace Core\App\Billing\Invoice;

use App\Services\Money\MoneyService;
use Core\Domains\Account\AccountIdEnum;
use Core\Domains\Account\AccountService;
use Core\Domains\Billing\Claim\ClaimFactory;
use Core\Domains\Billing\Claim\ClaimService;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Invoice\InvoiceSearcher;
use Core\Domains\Billing\Invoice\InvoiceTypeEnum;
use Core\Domains\Billing\Service\ServiceCatalogService;
use Core\Domains\Billing\Service\ServiceTypeEnum;

readonly class SyncPeriodServicesCommand
{
    public function __construct(
        private InvoiceService          $invoiceService,
        private ClaimService            $claimService,
        private ClaimFactory            $claimFactory,
        private AccountService          $accountService,
        private ServiceCatalogService   $serviceService,
        private RecalcClaimsPaidCommand $claimsPaidCommand,
    )
    {
    }

    public function execute(int $periodId): array
    {
        $periodServices = $this->serviceService->getByPeriodId($periodId)
            ->filter(static fn($s) => in_array($s->getType(), ServiceTypeEnum::claimableForInvoice(), true))
        ;

        if ($periodServices->isEmpty()) {
            return ['added' => 0, 'processed' => 0];
        }

        $invoices = $this->invoiceService->search(
            InvoiceSearcher::make()
                ->setPeriodId($periodId)
                ->setType(InvoiceTypeEnum::REGULAR),
        )->getItems();

        $added     = 0;
        $processed = 0;

        foreach ($invoices as $invoice) {
            if ($invoice->getAccountId() === AccountIdEnum::SNT->value) {
                continue;
            }

            $processed++;
            $existingServiceIds = $this->claimService->getByInvoiceIdSorted($invoice->getId())
                ->map(static fn($c) => $c->getServiceId())
                ->toArray()
            ;

            $hasNew = false;

            foreach ($periodServices as $service) {
                if (in_array($service->getId(), $existingServiceIds, true)) {
                    continue;
                }

                if ( ! $service->getCost()) {
                    continue;
                }

                [$cost, $size] = $this->calculateClaimCost($service, $invoice);

                $claim = $this->claimFactory->makeDefault()
                    ->setInvoiceId($invoice->getId())
                    ->setServiceId($service->getId())
                    ->setTariff($service->getCost())
                    ->setCost(MoneyService::toFloat($cost))
                    ->setQuantity($size)
                ;

                $this->claimService->save($claim);
                $added++;
                $hasNew = true;
            }

            if ($hasNew) {
                $this->claimsPaidCommand->execute($invoice->getId());
            }
        }

        return ['added' => $added, 'processed' => $processed];
    }

    private function calculateClaimCost($service, $invoice): array
    {
        $tariff = MoneyService::parse($service->getCost());

        if ($service->getType() === ServiceTypeEnum::MEMBERSHIP_FEE) {
            $size = (int) $this->accountService->getById($invoice->getAccountId())?->getSize();
            $cost = $tariff->multiply($size);
        }
        else {
            $cost = MoneyService::parse($service->getCost());
            $size = MoneyService::toInt($cost->divide(MoneyService::toFloat($tariff)));
        }

        return [$cost, $size];
    }
}
