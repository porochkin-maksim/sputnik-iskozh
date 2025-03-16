<?php declare(strict_types=1);

namespace Core\Domains\Billing\Jobs;

use App\Models\Billing\Transaction;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Billing\Invoice\InvoiceLocator;
use Core\Domains\Billing\Invoice\Models\InvoiceSearcher;
use Core\Domains\Billing\Payment\Collections\PaymentCollection;
use Core\Domains\Billing\Service\Enums\ServiceTypeEnum;
use Core\Domains\Billing\Transaction\Collections\TransactionCollection;
use Core\Domains\Billing\Transaction\Models\TransactionSearcher;
use Core\Domains\Billing\Transaction\TransactionLocator;
use Core\Queue\QueueEnum;
use Core\Services\Money\MoneyService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecalcTransactionsPayedJob implements ShouldQueue
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
        $searcher = new InvoiceSearcher();
        $searcher
            ->setId($this->invoiceId)
            ->setWithTransactions()
            ->setWithPayments();

        $invoice = InvoiceLocator::InvoiceService()->search($searcher)->getItems()->first();

        if ( ! $invoice) {
            return;
        }

        $transactions = $invoice->getTransactions() ? : new TransactionCollection();
        $totalPayed   = ($invoice->getPayments() ? : new PaymentCollection())
            ->getVerified()
            ->getTotalCostMoney();

        $transactionSearcher = new TransactionSearcher();
        $transactionSearcher
            ->setIds($transactions->getIds())
            ->setWithService()
            ->setSortOrderProperty(Transaction::SERVICE_ID, SearcherInterface::SORT_ORDER_ASC);

        $transactions = TransactionLocator::TransactionService()->search($transactionSearcher)->getItems();
        $transactions = $transactions->sortByServiceTypes([
            ServiceTypeEnum::MEMBERSHIP_FEE,
            ServiceTypeEnum::TARGET_FEE,
            ServiceTypeEnum::ELECTRIC_TARIFF,
            ServiceTypeEnum::OTHER,
        ]);

        foreach ($transactions as $transaction) {
            $transactionCost  = MoneyService::parse($transaction->getCost());
            $transactionPayed = MoneyService::parse(0);

            // если оплачено больше чем стоимость транзакции
            if ($totalPayed->subtract($transactionCost)->isPositive()) {
                // фиксируем, что транзакция оплачена полностью
                $transactionPayed = $transactionPayed->add($transactionCost);
            }
            else {
                // фиксируем, что транзакция оплачена частично
                $transactionPayed = $transactionPayed->add($totalPayed);
            }

            // вычитаем из платежей стоимость транзакции
            $totalPayed = $totalPayed->subtract($transactionPayed);

            $transaction->setPayed(MoneyService::toFloat($transactionPayed));
            if ($totalPayed->isZero()) {
                break;
            }
        }

        TransactionLocator::TransactionService()->saveCollection($transactions);
    }
}
