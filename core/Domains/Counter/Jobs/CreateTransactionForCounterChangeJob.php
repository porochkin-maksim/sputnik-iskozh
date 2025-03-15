<?php declare(strict_types=1);

namespace Core\Domains\Counter\Jobs;

use App\Models\Billing\Invoice;
use App\Models\Billing\Service;
use App\Models\Counter\Counter;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Billing\Invoice\Enums\InvoiceTypeEnum;
use Core\Domains\Billing\Invoice\InvoiceLocator;
use Core\Domains\Billing\Invoice\Models\InvoiceSearcher;
use Core\Domains\Billing\Period\PeriodLocator;
use Core\Domains\Billing\Service\Enums\ServiceTypeEnum;
use Core\Domains\Billing\Service\Models\ServiceSearcher;
use Core\Domains\Billing\Service\ServiceLocator;
use Core\Domains\Billing\Transaction\TransactionLocator;
use Core\Domains\Counter\CounterLocator;
use Core\Domains\Counter\Models\CounterSearcher;
use Core\Queue\QueueEnum;
use Core\Services\Money\MoneyService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateTransactionForCounterChangeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $counterHistoryId,
    )
    {
        $this->onQueue(QueueEnum::DEFAULT->value);
    }

    public function handle(): void
    {
        $history = CounterLocator::CounterHistoryService()->getById($this->counterHistoryId);
        if ( ! $history) {
            return;
        }

        $counterSearcher = new CounterSearcher();
        $counterSearcher
            ->setId($history->getCounterId())
            ->addWhere(Counter::IS_INVOICING, SearcherInterface::EQUALS, true)
        ;
        $counter = CounterLocator::CounterService()->search($counterSearcher)->getItems()->first();

        if ( ! $counter) {
            return;
        }

        $previous = null;

        foreach ($counter->getHistoryCollection()->sortById() as $item) {
            if ((int) $item->getId() >= (int) $history->getId()) {
                break;
            }

            $previous = $item;
        }

        if ( ! $previous || ! $previous->isVerified()) {
            return;
        }

        $delta = $history->getValue() - $previous->getValue();

        if ((float) $delta <= 0) {
            return;
        }

        $period = PeriodLocator::PeriodService()->getCurrentPeriod();

        if ( ! $period) {
            return;
        }

        $serviceSearcher = new ServiceSearcher();
        $serviceSearcher
            ->setPeriodId($period->getId())
            ->addWhere(Service::TYPE, SearcherInterface::EQUALS, ServiceTypeEnum::ELECTRIC_TARIFF->value)
        ;
        $service = ServiceLocator::ServiceService()->search($serviceSearcher)->getItems()->first();

        if ( ! $service || ! $service->getCost()) {
            return;
        }

        $invoiceSearcher = new InvoiceSearcher();
        $invoiceSearcher
            ->setPeriodId($period->getId())
            ->setAccountId($counter->getAccountId())
            ->setSortOrderProperty(Invoice::ID, SearcherInterface::SORT_ORDER_DESC);
        ;
        $invoices = InvoiceLocator::InvoiceService()->search($invoiceSearcher)->getItems();

        $linkingInvoice = null;
        foreach ($invoices as $invoice) {
            if ($invoice->getType() === InvoiceTypeEnum::REGULAR) {
                $linkingInvoice = $invoice;
                break;
            }
        }

        if ( ! $linkingInvoice) {
            $linkingInvoice = InvoiceLocator::InvoiceFactory()
                ->makeDefault()
                ->setType(InvoiceTypeEnum::INCOME)
                ->setPeriodId($period->getId())
                ->setAccountId($counter->getAccountId())
            ;
            $linkingInvoice = InvoiceLocator::InvoiceService()->save($linkingInvoice);
        }

        $deltaMoney = MoneyService::parse($delta)->multiply($service->getCost());

        $transaction = TransactionLocator::TransactionFactory()
            ->makeDefault()
            ->setInvoiceId($linkingInvoice->getId())
            ->setServiceId($service->getId())
            ->setTariff($service->getCost())
            ->setCost(MoneyService::toFloat($deltaMoney))
        ;

        TransactionLocator::TransactionService()->save($transaction);
    }
}
