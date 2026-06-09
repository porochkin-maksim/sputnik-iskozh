<?php declare(strict_types=1);

namespace Core\App\Billing\Invoice;

use App\Models\Billing\Period;
use App\Services\Money\MoneyService;
use Core\Domains\Account\AccountIdEnum;
use Core\Domains\Account\AccountService;
use Core\Domains\Billing\Claim\ClaimCollection;
use Core\Domains\Billing\Claim\ClaimEntity;
use Core\Domains\Billing\Claim\ClaimFactory;
use Core\Domains\Billing\Claim\ClaimSearcher;
use Core\Domains\Billing\Claim\ClaimService;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Invoice\InvoiceSearcher;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Invoice\InvoiceTypeEnum;
use Core\Domains\Billing\Payment\PaymentFactory;
use Core\Domains\Billing\Payment\PaymentService;
use Core\Domains\Billing\Period\PeriodSearcher;
use Core\Domains\Billing\Period\PeriodService;
use Core\Domains\Billing\Service\ServiceCatalogService;
use Core\Domains\Billing\Service\ServiceSearcher;
use Core\Domains\Billing\Service\ServiceTypeEnum;
use Core\Repositories\SearcherInterface;
use Illuminate\Support\Str;
use RuntimeException;

readonly class CreateClaimsAndPaymentsForRegularInvoiceCommand
{
    public function __construct(
        private InvoiceService        $invoiceService,
        private ClaimService          $claimService,
        private ClaimFactory          $claimFactory,
        private AccountService        $accountService,
        private ServiceCatalogService $serviceService,
        private PaymentFactory        $paymentFactory,
        private PaymentService        $paymentService,
        private PeriodService         $periodService,
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

        $oldClaims  = $this->getMigratingClaimsToNewPeriod($invoice);
        $oldAdvance = $oldClaims->getAdvancePayment();
        $oldDebts   = $oldClaims->filter(fn(ClaimEntity $claim) => ! $claim->getService()?->getType()?->isAdvance());

        $newPeriodServices = $this->serviceService->search(new ServiceSearcher()
            ->setPeriodId($invoice->getPeriodId())
            ->setActive(true))->getItems();
        $newDebtService    = $newPeriodServices->getByType(ServiceTypeEnum::DEBT)->first();

        $newClaims = new ClaimCollection();

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
                ->setInvoiceId($invoice->getId())
                ->setServiceId($newDebtService->getId())
                ->setTariff($oldDebtClaim->getTariff())
                ->setCost($oldDebtClaim->getDelta())
                ->setName($newServiceName)
                ->setPaid(0.00)
            ;

            $newClaims->add($this->claimService->save($claim));
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
                ->setQuantity($service->getType() === ServiceTypeEnum::MEMBERSHIP_FEE ? $size : null)
                ->setPaid(0.00)
            ;

            $newClaims->add($this->claimService->save($claim));
        }

        if ($oldAdvance?->getPaid()) {
            $payment = $this->paymentFactory->makeDefault()
                ->setAccountId($invoice->getAccountId())
                ->setInvoiceId($invoice->getId())
                ->setCost($oldAdvance->getPaid())
                ->setModerated(true)
                ->setVerified(true)
                ->setName('Аванс с предыдущего периода')
                ->setComment('Автоматический платёж из переплаты с предыдущего периода')
            ;

            $this->paymentService->save($payment);
        }
    }

    private function getMigratingClaimsToNewPeriod(InvoiceEntity $invoice): ClaimCollection
    {
        $result = new ClaimCollection();

        $period = $this->periodService->search(
            PeriodSearcher::make()
                ->setSortOrderProperty(Period::ID, SearcherInterface::SORT_ORDER_DESC)
                ->addWhere(Period::ID, SearcherInterface::LT, $invoice->getPeriodId())
                ->setLimit(1),
        )->getItems()->first();

        if ($period === null) {
            return $result;
        }

        $previousInvoice = $this->invoiceService->search(
            InvoiceSearcher::make()
                ->setPeriodId($period->getId())
                ->setAccountId($invoice->getAccountId())
                ->setType(InvoiceTypeEnum::REGULAR)
                ->setLimit(1),
        )->getItems()->first();

        if ($previousInvoice === null) {
            return $result;
        }

        $previousInvoice->setPeriod($period);

        return $this->claimService->search(
            ClaimSearcher::make()
                ->setWithService()
                ->setInvoiceId($previousInvoice->getId()),
        )->getItems()
            ->map(static fn(ClaimEntity $claim) => $claim->setInvoice($previousInvoice))
            ->filter(static fn(ClaimEntity $claim) => $claim->getDelta() || $claim->getService()?->getType()?->isAdvance())
        ;
    }
}
