<?php declare(strict_types=1);

namespace App\Jobs\Billing;

use App\Models\Billing\Period;
use App\Services\Money\MoneyService;
use App\Services\Queue\DispatchIfNeededTrait;
use App\Services\Queue\QueueEnum;
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
use Core\Domains\Billing\Service\ServiceSearcher;
use Core\Domains\Billing\Service\ServiceCatalogService;
use Core\Domains\Billing\Service\ServiceTypeEnum;
use Core\Domains\Infra\DbLock\Enum\LockNameEnum;
use Core\Repositories\SearcherInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Создаёт услуги и платежи для регулярного счёта
 */
class CreateClaimsAndPaymentsForRegularInvoiceJob implements ShouldQueue
{
    use DispatchIfNeededTrait;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $invoiceId,
    )
    {
        $this->onQueue(QueueEnum::DEFAULT->value);
    }

    protected static function getLockName(): LockNameEnum
    {
        return LockNameEnum::CREATE_CLAIMS_AND_PAYMENTS_FOR_REGULAR_INVOICE_JOB;
    }

    protected function getIdentificator(): null|int|string
    {
        return $this->invoiceId;
    }

    protected function process(
        InvoiceService        $invoiceService,
        ClaimService          $claimService,
        ClaimFactory          $claimFactory,
        AccountService        $accountService,
        ServiceCatalogService $serviceService,
        PaymentFactory        $paymentFactory,
        PaymentService        $paymentService,
        PeriodService         $periodService,
    ): void
    {
        $invoice = $invoiceService->getById($this->invoiceId);
        if ( ! $invoice) {
            throw new RuntimeException("Счёт не найден #{$this->invoiceId}");
        }

        if ($invoice->getType() !== InvoiceTypeEnum::REGULAR) {
            return;
        }

        if ($invoice->getAccountId() === AccountIdEnum::SNT->value) {
            return;
        }

        $oldClaims = $this->getMigratingClaimsToNewPeriod($invoice, $periodService, $invoiceService, $claimService);

        $oldAdvance = $oldClaims->getAdvancePayment();
        $oldDebts   = $oldClaims->filter(fn(ClaimEntity $claim) => ! $claim->getService()?->getType()?->isAdvance());

        $newPeriodServices = $serviceService->search((new ServiceSearcher())
            ->setPeriodId($invoice->getPeriodId())
            ->setActive(true),
        )->getItems();

        $newDebtService = $newPeriodServices->getByType(ServiceTypeEnum::DEBT)->first();

        $newClaims = new ClaimCollection();

        foreach ($oldDebts as $oldDebtClaim) {
            $oldService = $oldDebtClaim->getService();
            if (Str::contains('(долг за период', $oldService?->getName())) {
                $newServiceName = $oldService?->getName();
            }
            else {
                $newServiceName = sprintf('%s (долг за период %s)',
                    $oldService?->getName() ? : $oldService?->getType()?->name(),
                    $oldDebtClaim->getInvoice()?->getPeriod()?->getName(),
                );
            }

            $claim = $claimFactory
                ->makeDefault()
                ->setInvoiceId($this->invoiceId)
                ->setServiceId($newDebtService->getId())
                ->setTariff($oldDebtClaim->getTariff())
                ->setCost($oldDebtClaim->getDelta())
                ->setName($newServiceName)
                ->setPaid(0.00)
            ;

            $newClaims->add($claimService->save($claim));
        }

        foreach ($newPeriodServices as $service) {
            if ( ! in_array($service->getType(), [
                ServiceTypeEnum::MEMBERSHIP_FEE,
                ServiceTypeEnum::TARGET_FEE,
                ServiceTypeEnum::PERSONAL_FEE,
            ], true)) {
                continue;
            }
            if ($service->getType() === ServiceTypeEnum::MEMBERSHIP_FEE) {
                $tariff = MoneyService::parse($service->getCost());
                $size   = (int) $accountService->getById($invoice->getAccountId())?->getSize();
                $cost   = $tariff->multiply($size);
            }
            else {
                $cost = MoneyService::parse($service->getCost());
            }

            $claim = $claimFactory
                ->makeDefault()
                ->setInvoiceId($this->invoiceId)
                ->setServiceId($service->getId())
                ->setTariff($service->getCost())
                ->setCost(MoneyService::toFloat($cost))
                ->setPaid(0.00)
            ;

            $newClaims->add($claimService->save($claim));
        }

        if ($oldAdvance?->getPaid()) {
            $payment = $paymentFactory
                ->makeDefault()
                ->setAccountId($invoice->getAccountId())
                ->setInvoiceId($invoice->getId())
                ->setCost($oldAdvance?->getPaid())
                ->setModerated(true)
                ->setVerified(true)
                ->setName('Аванс с предыдущего периода')
                ->setComment('Автоматический платёж из переплаты с предыдущего периода')
            ;

            $paymentService->save($payment);
        }
    }

    /**
     * Услуги переходящие в долг и оплату (аванс) нового счёта
     */
    private function getMigratingClaimsToNewPeriod(
        InvoiceEntity  $invoice,
        PeriodService  $periodService,
        InvoiceService $invoiceService,
        ClaimService   $claimService,
    ): ClaimCollection
    {
        $result = new ClaimCollection();

        $periodSearcher = PeriodSearcher::make()
            ->setSortOrderProperty(Period::ID, SearcherInterface::SORT_ORDER_DESC)
            ->addWhere(Period::ID, SearcherInterface::LT, $invoice->getPeriodId())
            ->setLimit(1)
        ;

        $period = $periodService->search($periodSearcher)->getItems()->first();

        if ( ! $period) {
            return $result;
        }

        $previousInvoice = $invoiceService->search(
            InvoiceSearcher::make()
                ->setPeriodId($period->getId())
                ->setAccountId($invoice->getAccountId())
                ->setType(InvoiceTypeEnum::REGULAR)
                ->setLimit(1),
        )->getItems()->first();

        if ( ! $previousInvoice) {
            return $result;
        }

        $previousInvoice->setPeriod($period);

        return $claimService->search((new ClaimSearcher())
            ->setWithService()
            ->setInvoiceId($previousInvoice->getId()),
        )->getItems()
            ->map(static function (ClaimEntity $claim) use ($previousInvoice) {
                return $claim->setInvoice($previousInvoice);
            })
            ->filter(static function (ClaimEntity $claim) {
                return $claim->getDelta() || ($claim->getService()?->getType()?->isAdvance());
            })
        ;
    }
}
