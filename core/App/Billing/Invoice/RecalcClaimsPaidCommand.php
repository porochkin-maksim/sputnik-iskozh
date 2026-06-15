<?php declare(strict_types=1);

namespace Core\App\Billing\Invoice;

use App\Models\Billing\Claim;
use App\Services\Money\MoneyService;
use Core\Domains\Billing\Claim\ClaimFactory;
use Core\Domains\Billing\Claim\ClaimSearcher;
use Core\Domains\Billing\Claim\ClaimService;
use Core\Domains\Billing\Invoice\InvoiceSearcher;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Payment\PaymentCollection;
use Core\Domains\Billing\Service\ServiceCatalogService;
use Core\Domains\Billing\Service\ServiceSearcher;
use Core\Domains\Billing\Service\ServiceTypeEnum;
use Core\Repositories\SearcherInterface;

readonly class RecalcClaimsPaidCommand
{
    public function __construct(
        private InvoiceService        $invoiceService,
        private ClaimService          $claimService,
        private ServiceCatalogService $serviceService,
        private ClaimFactory          $claimFactory,
    )
    {
    }

    public function execute(int $invoiceId): void
    {
        $searcher = new InvoiceSearcher();
        $searcher
            ->setId($invoiceId)
            ->setWithClaims()
            ->setWithPayments()
        ;

        $invoice = $this->invoiceService->search($searcher)->getItems()->first();

        if ($invoice === null) {
            return;
        }

        $totalPaid = ($invoice->getPayments() ? : new PaymentCollection())
            ->getVerified()
            ->getTotalCostMoney()
        ;

        $sortedClaims = $this->claimService->search(new ClaimSearcher()
            ->setInvoiceId($invoice->getId())
            ->setWithService()
            ->setSortOrderProperty(Claim::SERVICE_ID, SearcherInterface::SORT_ORDER_ASC))
            ->getItems()
            ->sortByServiceTypes()
        ;

        $advanceClaim = $sortedClaims->getAdvancePayment();

        foreach ($sortedClaims as $claim) {
            $claim->setPaid(0);
            $claim->setQuantity($claim->getQuantity() ?: 1.00);
            $claim->setCost((float) $claim->getTariff() * (float) $claim->getQuantity());
        }

        $remaining = $totalPaid;
        foreach ($sortedClaims as $claim) {
            if ($claim->getId() === $advanceClaim?->getId()) {
                continue;
            }

            $claimCost = MoneyService::parse($claim->getCost());
            $claimPaid = $remaining->subtract($claimCost)->isPositive() ? $claimCost : $remaining;

            $remaining = $remaining->subtract($claimPaid);
            $claim->setPaid(MoneyService::toFloat($claimPaid));

            if ($remaining->isZero()) {
                break;
            }
        }

        if ($remaining->isPositive()) {
            $service = $this->serviceService->search(
                ServiceSearcher::make()
                    ->setPeriodId($invoice->getPeriodId())
                    ->setType(ServiceTypeEnum::ADVANCE_PAYMENT),
            )->getItems()->first();

            if ($service !== null) {
                if ($advanceClaim !== null) {
                    $advanceClaim
                        ->setTariff(MoneyService::toFloat($remaining))
                        ->setCost(MoneyService::toFloat($remaining))
                        ->setPaid(MoneyService::toFloat($remaining))
                        ->setName('Аванс')
                    ;
                }
                else {
                    $advanceClaim = $this->claimFactory->makeDefault()
                        ->setInvoiceId($invoice->getId())
                        ->setServiceId($service->getId())
                        ->setCost(MoneyService::toFloat($remaining))
                        ->setPaid(MoneyService::toFloat($remaining))
                        ->setName('Аванс')
                    ;
                    $sortedClaims->push($advanceClaim);
                }
            }
        }
        elseif ($advanceClaim !== null && $advanceClaim->getPaid() > 0) {
            $sortedClaims->removeById($advanceClaim->getId());
            $this->claimService->deleteById($advanceClaim->getId());
        }

        $savedClaims = $this->claimService->saveCollection($sortedClaims);

        $totalCost    = MoneyService::parse(0);
        $totalPaidSum = MoneyService::parse(0);
        foreach ($savedClaims as $claim) {
            $totalCost    = $totalCost->add(MoneyService::parse($claim->getCost()));
            $totalPaidSum = $totalPaidSum->add(MoneyService::parse($claim->getPaid()));
        }

        $invoice->setCost(MoneyService::toFloat($totalCost));
        $invoice->setPaid(MoneyService::toFloat($totalPaidSum));
        $invoice->setAdvance((float) $advanceClaim?->getCost());
        $invoice->setDebt((float) $sortedClaims->getDebts()?->getCost());

        $this->invoiceService->save($invoice);
    }
}
