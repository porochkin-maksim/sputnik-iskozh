<?php declare(strict_types=1);

namespace Core\Domains\Billing\Invoice\Jobs;

use Core\Domains\Account\Jobs\UpdateSntBalanceJob;
use Core\Domains\Billing\Invoice\Enums\InvoiceTypeEnum;
use Core\Domains\Billing\Invoice\InvoiceLocator;
use Core\Domains\Billing\Invoice\Models\InvoiceSearcher;
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

class RecalcInvoiceJob implements ShouldQueue
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
        ;

        $invoice = InvoiceLocator::InvoiceService()->search($searcher)->getItems()->first();

        if ( ! $invoice) {
            return;
        }

        $oldTotalPayed = MoneyService::parse($invoice->getPayed());

        $totalCost  = MoneyService::parse(0);
        $totalPayed = MoneyService::parse(0);
        foreach ($invoice->getTransactions() ? : [] as $transaction) {
            $cost      = MoneyService::parse($transaction->getCost());
            $totalCost = $totalCost->add($cost);

            $payed      = MoneyService::parse($transaction->getPayed());
            $totalPayed = $totalPayed->add($payed);
        }
        $invoice->setCost(MoneyService::toFloat($totalCost));
        $invoice->setPayed(MoneyService::toFloat($totalPayed));

        HistoryChangesLocator::HistoryChangesService()->writeToHistory(
            Event::COMMON,
            HistoryType::INVOICE,
            $invoice->getId(),
            text: 'Произведён перерасчёт',
        );

        InvoiceLocator::InvoiceService()->save($invoice);


        if ($invoice->getType() === InvoiceTypeEnum::OUTCOME) {
            $difference = $oldTotalPayed->subtract($totalPayed);
        }
        else {
            $difference = $totalPayed->subtract($oldTotalPayed);
        }
        UpdateSntBalanceJob::dispatch(MoneyService::toFloat($difference));
    }
}
