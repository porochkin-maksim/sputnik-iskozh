<?php declare(strict_types=1);

namespace Core\Domains\Billing\Jobs;

use App\Models\Billing\Period;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Account\AccountLocator;
use Core\Domains\Account\Enums\AccountIdEnum;
use Core\Domains\Billing\Claim\Collections\ClaimCollection;
use Core\Domains\Billing\Claim\Models\ClaimDTO;
use Core\Domains\Billing\Claim\Models\ClaimSearcher;
use Core\Domains\Billing\Invoice\Enums\InvoiceTypeEnum;
use Core\Domains\Billing\Invoice\InvoiceLocator;
use Core\Domains\Billing\Invoice\Models\InvoiceDTO;
use Core\Domains\Billing\Invoice\Models\InvoiceSearcher;
use Core\Domains\Billing\Payment\PaymentLocator;
use Core\Domains\Billing\Period\Models\PeriodSearcher;
use Core\Domains\Billing\Period\PeriodLocator;
use Core\Domains\Billing\Service\Enums\ServiceTypeEnum;
use Core\Domains\Billing\Service\Models\ServiceSearcher;
use Core\Domains\Billing\Service\ServiceLocator;
use Core\Domains\Billing\Claim\ClaimLocator;
use Core\Queue\QueueEnum;
use Core\Services\Money\MoneyService;
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
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $invoiceId,
    )
    {
        $this->onQueue(QueueEnum::DEFAULT->value);
    }

    public function handle(): void
    {
        $invoice = InvoiceLocator::InvoiceService()->getById($this->invoiceId);
        if ( ! $invoice) {
            throw new RuntimeException("Счёт не найден #{$this->invoiceId}");
        }

        if ($invoice->getType() !== InvoiceTypeEnum::REGULAR) {
            return;
        }

        if ($invoice->getAccountId() === AccountIdEnum::SNT->value) {
            return;
        }

        $claimService   = ClaimLocator::ClaimService();
        $claimFactory   = ClaimLocator::ClaimFactory();
        $accountService = AccountLocator::AccountService();

        $oldClaims = $this->getMigratingClaimsToNewPeriod($invoice);

        $oldAdvance = $oldClaims->getAdvancePayment();
        $oldDebts   = $oldClaims->filter(fn(ClaimDTO $claim) => ! $claim->getService()?->getType()?->isAdvance());

        $newPeriodServices = ServiceLocator::ServiceService()->search(new ServiceSearcher()
            ->setPeriodId($invoice->getPeriodId())
            ->setActive(true),
        )->getItems();

        $newDebtService = $newPeriodServices->getByType(ServiceTypeEnum::DEBT)->first();

        $newClaims = new ClaimCollection();

        foreach ($oldDebts as $oldDebtClaim) {
            $oldService     = $oldDebtClaim->getService();
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
            $payment = PaymentLocator::PaymentFactory()
                ->makeDefault()
                ->setAccountId($invoice->getAccountId())
                ->setInvoiceId($invoice->getId())
                ->setCost($oldAdvance?->getPaid())
                ->setModerated(true)
                ->setVerified(true)
                ->setName('Аванс с предыдущего периода')
                ->setComment('Автоматический платёж из переплаты с предыдущего периода')
            ;

            PaymentLocator::PaymentService()->save($payment);
        }
    }

    /**
     * Услуги переходящие в долг и оплату (аванс) нового счёта
     */
    private function getMigratingClaimsToNewPeriod(InvoiceDTO $invoice): ClaimCollection
    {
        $result = new ClaimCollection();

        $periodSearcher = PeriodSearcher::make()
            ->setSortOrderProperty(Period::ID, SearcherInterface::SORT_ORDER_DESC)
            ->addWhere(Period::ID, SearcherInterface::LT, $invoice->getPeriodId())
            ->setLimit(1)
        ;

        $period = PeriodLocator::PeriodService()->search($periodSearcher)->getItems()->first();

        if ( ! $period) {
            return $result;
        }

        $previousInvoice = InvoiceLocator::InvoiceService()->search(
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

        return ClaimLocator::ClaimService()->search(new ClaimSearcher()
            ->setWithService()
            ->setInvoiceId($previousInvoice->getId()),
        )->getItems()
            ->map(static function (ClaimDTO $claim) use ($previousInvoice) {
                return $claim->setInvoice($previousInvoice);
            })
            ->filter(static function (ClaimDTO $claim) {
                return $claim->getDelta() || ($claim->getService(true)?->getType()?->isAdvance());
            })
        ;
    }
}
