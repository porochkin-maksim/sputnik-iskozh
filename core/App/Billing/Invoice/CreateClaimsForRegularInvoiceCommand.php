<?php declare(strict_types=1);

namespace Core\App\Billing\Invoice;

use App\Services\Money\MoneyService;
use Core\Domains\Account\AccountService;
use Core\Domains\Billing\Claim\ClaimFactory;
use Core\Domains\Billing\Claim\ClaimService;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Service\ServiceCatalogService;
use Core\Domains\Billing\Service\ServiceTypeEnum;

readonly class CreateClaimsForRegularInvoiceCommand
{
    public function __construct(
        private InvoiceService        $invoiceService,
        private ClaimService          $claimService,
        private ClaimFactory          $claimFactory,
        private AccountService        $accountService,
        private ServiceCatalogService $serviceService,
    )
    {
    }

    public function execute(int $invoiceId): void
    {
        $invoice = $this->invoiceService->getById($invoiceId);
        if ($invoice === null) {
            return;
        }

        $newPeriodServices = $this->serviceService->getByPeriodId($invoice->getPeriodId());

        foreach ($newPeriodServices as $service) {
            if ( ! in_array($service->getType(), ServiceTypeEnum::claimableForInvoice(), true)) {
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

            $this->claimService->save($claim);
        }
    }
}
