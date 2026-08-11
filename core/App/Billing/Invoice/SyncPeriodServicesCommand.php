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
        $allPeriodServices = $this->serviceService->getByPeriodId($periodId);

        if ($allPeriodServices->isEmpty()) {
            return ['added' => 0, 'removed' => 0, 'skipped' => 0, 'processed' => 0];
        }

        $allPeriodServiceIds = $allPeriodServices
            ->map(static fn($service) => $service->getId())
            ->toArray()
        ;

        $periodServices = $allPeriodServices->filter(
            static fn($service) => in_array($service->getType(), ServiceTypeEnum::claimableForInvoice(), true),
        );

        $invoices = $this->invoiceService->search(
            InvoiceSearcher::make()
                ->setPeriodId($periodId)
                ->setType(InvoiceTypeEnum::REGULAR),
        )->getItems();

        $added     = 0;
        $removed   = 0;
        $skipped   = 0;
        $processed = 0;

        foreach ($invoices as $invoice) {
            if ($invoice->getAccountId() === AccountIdEnum::SNT->value) {
                continue;
            }

            $processed++;

            $claims = $this->claimService->getByInvoiceIdSorted($invoice->getId());

            foreach ($claims as $claim) {
                if ($claim->getOriginalClaimId() !== null) {
                    continue;
                }

                if (in_array($claim->getServiceId(), $allPeriodServiceIds, true)) {
                    continue;
                }

                if ((float) $claim->getPaid() > 0) {
                    $skipped++;
                    continue;
                }

                $this->claimService->deleteById($claim->getId());
                $removed++;
            }

            $existingServiceIds = $claims
                ->map(static fn($claim) => $claim->getServiceId())
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

        return [
            'added'     => $added,
            'removed'   => $removed,
            'skipped'   => $skipped,
            'processed' => $processed,
        ];
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
