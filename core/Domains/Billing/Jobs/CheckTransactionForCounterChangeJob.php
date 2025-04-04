<?php declare(strict_types=1);

namespace Core\Domains\Billing\Jobs;

use App\Models\Billing\Invoice;
use App\Models\Billing\Service;
use App\Models\Counter\Counter;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Account\AccountLocator;
use Core\Domains\Billing\Invoice\Enums\InvoiceTypeEnum;
use Core\Domains\Billing\Invoice\InvoiceLocator;
use Core\Domains\Billing\Invoice\Models\InvoiceSearcher;
use Core\Domains\Billing\Period\PeriodLocator;
use Core\Domains\Billing\Service\Enums\ServiceTypeEnum;
use Core\Domains\Billing\Service\Models\ServiceSearcher;
use Core\Domains\Billing\Service\ServiceLocator;
use Core\Domains\Billing\Transaction\Models\TransactionDTO;
use Core\Domains\Billing\Transaction\TransactionLocator;
use Core\Domains\Billing\TransactionToObject\Enums\TransactionObjectTypeEnum;
use Core\Domains\Billing\TransactionToObject\TransactionToObjectLocator;
use Core\Domains\Counter\CounterLocator;
use Core\Domains\Counter\Models\CounterSearcher;
use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Queue\QueueEnum;
use Core\Services\Money\MoneyService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

/**
 * Процедура проверяет изменились ли показания привязанного счетчика и пересчитывает стоимость и тариф по текущему курсу
 */
class CheckTransactionForCounterChangeJob implements ShouldQueue
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
        $transaction = TransactionToObjectLocator::TransactionToObjectService()
            ->getByReference(TransactionObjectTypeEnum::COUNTER_HISTORY, $this->counterHistoryId)
        ;

        $history = CounterLocator::CounterHistoryService()->getById($this->counterHistoryId);
        if ( ! $history) {
            $this->deleteTransaction($transaction);
            return;
        }

        $counterSearcher = new CounterSearcher();
        $counterSearcher
            ->setId($history->getCounterId())
            ->addWhere(Counter::IS_INVOICING, SearcherInterface::EQUALS, true)
        ;
        $counter = CounterLocator::CounterService()->search($counterSearcher)->getItems()->first();

        if ( ! $counter) {
            $this->deleteTransaction($transaction);
            return;
        }

        $previous = CounterLocator::CounterHistoryService()->getPrevios($history);

        if ( ! $previous || ! $previous->isVerified()) {
            $this->deleteTransaction($transaction);
            return;
        }

        $delta = $history->getValue() - $previous->getValue();

        if ((float) $delta <= 0) {
            $this->deleteTransaction($transaction);
            return;
        }

        $period = PeriodLocator::PeriodService()->getCurrentPeriod();

        if ( ! $period) {
            $this->deleteTransaction($transaction);
            return;
        }

        $serviceSearcher = new ServiceSearcher();
        $serviceSearcher
            ->setPeriodId($period->getId())
            ->addWhere(Service::TYPE, SearcherInterface::EQUALS, ServiceTypeEnum::ELECTRIC_TARIFF->value)
        ;
        $service = ServiceLocator::ServiceService()->search($serviceSearcher)->getItems()->first();

        if ( ! $service || ! $service->getCost()) {
            $this->deleteTransaction($transaction);
            return;
        }

        $account = AccountLocator::AccountService()->getById($counter->getAccountId());

        if ( ! $account || $account->isSnt()) {
            $this->deleteTransaction($transaction);
            return;
        }

        $invoiceSearcher = new InvoiceSearcher();
        $invoiceSearcher
            ->setPeriodId($period->getId())
            ->setAccountId($account->getId())
            ->setSortOrderProperty(Invoice::ID, SearcherInterface::SORT_ORDER_DESC)
        ;
        $invoices = InvoiceLocator::InvoiceService()->search($invoiceSearcher)->getItems();

        /**
         *  для аккаунта СНТ всегда будет расход, а для других аккаунтов "доход в пользу СНТ"
         *  т.к. расход подразумевает вывод денег из СНТ
         * а доход и регулярный счета - взносы денег в СНТ
         */
        $linkingInvoice = null;
        foreach ($invoices as $invoice) {
            if ( ! $account->isSnt()) {
                if ($invoice->getType() === InvoiceTypeEnum::REGULAR) {
                    $linkingInvoice = $invoice;
                    break;
                }
            }
            elseif ($invoice->getType() === InvoiceTypeEnum::OUTCOME) {
                $linkingInvoice = $invoice;
                break;
            }
        }

        DB::beginTransaction();

        try {
            $transactionExists = (bool) $transaction;

            if ( ! $transactionExists) {
                if ( ! $linkingInvoice) {
                    $linkingInvoice = InvoiceLocator::InvoiceFactory()
                        ->makeDefault()
                        ->setType($account->isSnt() ? InvoiceTypeEnum::OUTCOME : InvoiceTypeEnum::INCOME)
                        ->setPeriodId($period->getId())
                        ->setAccountId($account->getId())
                    ;
                    $linkingInvoice = InvoiceLocator::InvoiceService()->save($linkingInvoice);
                }

                $transaction = TransactionLocator::TransactionFactory()
                    ->makeDefault()
                    ->setInvoiceId($linkingInvoice->getId())
                    ->setServiceId($service->getId())
                    ->setTariff($service->getCost())
                    ->setName(sprintf('Оплата %s кВт по счётчику "%s"', $delta, $counter->getNumber()))
                ;
            }
            else {
                $transaction->setName(sprintf('Оплата %s кВт по счётчику "%s"', $delta, $counter->getNumber()));
            }


            $deltaMoney = MoneyService::parse($delta)->multiply($service->getCost());
            $hasChanges = $transaction->getCost() !== MoneyService::toFloat($deltaMoney);
            $transaction->setCost(MoneyService::toFloat($deltaMoney));
            $transaction = TransactionLocator::TransactionService()->save($transaction);

            if ($hasChanges) {
                $message = sprintf("%s автоматическа транзакция\n при показаниях счётчика \"%s\",\n участка \"%s\"\n на +%s кВт по тарифу %s",
                    $transactionExists ? 'Обновлена' : 'Создана',
                    $counter->getNumber(),
                    $account->getNumber(),
                    $delta,
                    MoneyService::parse($service->getCost()),
                );

                HistoryChangesLocator::HistoryChangesService()->writeToHistory(
                    Event::COMMON,
                    HistoryType::INVOICE,
                    $linkingInvoice->getId(),
                    HistoryType::TRANSACTION,
                    $transaction->getId(),
                    text: $message,
                );
            }

            if ( ! TransactionToObjectLocator::TransactionToObjectService()->hasRelations($transaction)) {
                TransactionToObjectLocator::TransactionToObjectService()->create($transaction, $history->getId(), TransactionObjectTypeEnum::COUNTER_HISTORY);
            }

            DB::commit();
        }
        catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    private function deleteTransaction(?TransactionDTO $transaction): void
    {
        if ($transaction) {
            TransactionLocator::TransactionService()->deleteById($transaction->getId());
        }
    }
}
