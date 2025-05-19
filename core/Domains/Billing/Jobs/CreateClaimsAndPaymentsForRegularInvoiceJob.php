<?php declare(strict_types=1);

namespace Core\Domains\Billing\Jobs;

use App\Models\Billing\Period;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Account\AccountLocator;
use Core\Domains\Account\Enums\AccountIdEnum;
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
use Core\Domains\Billing\Claim\Events\ClaimsUpdatedEvent;
use Core\Domains\Billing\Claim\ClaimLocator;
use Core\Queue\QueueEnum;
use Core\Services\Money\MoneyService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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

        $serviceSearcher = new ServiceSearcher();
        $serviceSearcher
            ->setPeriodId($invoice->getPeriodId())
            ->setActive(true)
        ;

        $balance = $this->getBalanceFromPreviousPeriod($invoice);

        foreach (ServiceLocator::ServiceService()->search($serviceSearcher)->getItems() as $service) {
            if ( ! in_array($service->getType(), [
                ServiceTypeEnum::DEBT,
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
            elseif ($service->getType() === ServiceTypeEnum::DEBT) {
                $cost = MoneyService::parse($balance < 0 ? abs($balance) : 0);
            }
            else {
                $cost = MoneyService::parse($service->getCost());
            }

            $claim = $claimFactory->makeDefault();
            $claim->setInvoiceId($this->invoiceId)
                ->setServiceId($service->getId())
                ->setTariff($service->getCost())
                ->setCost(MoneyService::toFloat($cost))
                ->setPayed(0.00)
            ;

            $claimService->save($claim);
        }

        if ($balance > 0) {
            $payment = PaymentLocator::PaymentFactory()
                ->makeDefault()
                ->setAccountId($invoice->getAccountId())
                ->setInvoiceId($invoice->getId())
                ->setCost($balance)
                ->setModerated(true)
                ->setVerified(true)
                ->setName('Аванс с предыдущего периода')
                ->setComment('Автоматический платёж из переплаты с предыдущего периода')
            ;

            PaymentLocator::PaymentService()->save($payment);
        }

        ClaimsUpdatedEvent::dispatch($invoice->getId());
    }

    /**
     * Разница сколько должен с предыдущего периода.
     * 0    - всё оплачено.
     * > 0  - аванс/переплата.
     * < 0  - долг.
     */
    private function getBalanceFromPreviousPeriod(InvoiceDTO $invoice): float
    {
        $periodSearcher = PeriodSearcher::make()
            ->setSortOrderProperty(Period::ID, SearcherInterface::SORT_ORDER_DESC)
            ->addWhere(Period::ID, SearcherInterface::LT, $invoice->getPeriodId())
            ->setLimit(1)
        ;

        $period = PeriodLocator::PeriodService()->search($periodSearcher)->getItems()->first();

        if ( ! $period || ! $period->isClosed()) {
            return 0;
        }

        $previousInvoice = InvoiceLocator::InvoiceService()->search(
            InvoiceSearcher::make()
                ->setPeriodId($period->getId())
                ->setAccountId($invoice->getAccountId())
                ->setType(InvoiceTypeEnum::REGULAR)
                ->setLimit(1),
        )->getItems()->first();

        if ( ! $previousInvoice) {
            return 0;
        }

        $claims = ClaimLocator::ClaimService()->search(
            ClaimSearcher::make()
                ->setWithService()
                ->setInvoiceId($previousInvoice->getId()),
        )->getItems();

        $debt = $previousInvoice->getCost() - $previousInvoice->getPayed();

        /**
         * Если по нулям, то там может быть аванс
         */
        if ((float) $debt === 0.0) {
            return (float) $claims->findByServiceType(ServiceTypeEnum::ADVANCE_PAYMENT)?->getCost();
        }

        /**
         * это долг
         */
        return -1 * $debt;
    }
}
