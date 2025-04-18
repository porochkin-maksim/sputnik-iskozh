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
use Core\Domains\Billing\Claim\Models\ClaimDTO;
use Core\Domains\Billing\Claim\ClaimLocator;
use Core\Domains\Billing\ClaimToObject\Enums\ClaimObjectTypeEnum;
use Core\Domains\Billing\ClaimToObject\ClaimToObjectLocator;
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
class CheckClaimForCounterChangeJob implements ShouldQueue
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
        $claim = ClaimToObjectLocator::ClaimToObjectService()
            ->getByReference(ClaimObjectTypeEnum::COUNTER_HISTORY, $this->counterHistoryId)
        ;

        $history = CounterLocator::CounterHistoryService()->getById($this->counterHistoryId);
        if ( ! $history) {
            $this->deleteClaim($claim);
            return;
        }

        $counterSearcher = new CounterSearcher();
        $counterSearcher
            ->setId($history->getCounterId())
            ->addWhere(Counter::IS_INVOICING, SearcherInterface::EQUALS, true)
        ;
        $counter = CounterLocator::CounterService()->search($counterSearcher)->getItems()->first();

        if ( ! $counter) {
            $this->deleteClaim($claim);
            return;
        }

        $previous = CounterLocator::CounterHistoryService()->getPrevious($history);

        if ( ! $previous || ! $previous->isVerified()) {
            $this->deleteClaim($claim);
            return;
        }

        $delta = $history->getValue() - $previous->getValue();

        if ((float) $delta <= 0) {
            $this->deleteClaim($claim);
            return;
        }

        $period = PeriodLocator::PeriodService()->getCurrentPeriod();

        if ( ! $period) {
            $this->deleteClaim($claim);
            return;
        }

        $serviceSearcher = new ServiceSearcher();
        $serviceSearcher
            ->setPeriodId($period->getId())
            ->addWhere(Service::TYPE, SearcherInterface::EQUALS, ServiceTypeEnum::ELECTRIC_TARIFF->value)
        ;
        $service = ServiceLocator::ServiceService()->search($serviceSearcher)->getItems()->first();

        if ( ! $service || ! $service->getCost()) {
            $this->deleteClaim($claim);
            return;
        }

        $account = AccountLocator::AccountService()->getById($counter->getAccountId());

        if ( ! $account || $account->isSnt()) {
            $this->deleteClaim($claim);
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
            $claimExists = (bool) $claim;

            if ( ! $claimExists) {
                if ( ! $linkingInvoice) {
                    $linkingInvoice = InvoiceLocator::InvoiceFactory()
                        ->makeDefault()
                        ->setType($account->isSnt() ? InvoiceTypeEnum::OUTCOME : InvoiceTypeEnum::INCOME)
                        ->setPeriodId($period->getId())
                        ->setAccountId($account->getId())
                    ;
                    $linkingInvoice = InvoiceLocator::InvoiceService()->save($linkingInvoice);
                }

                $claim = ClaimLocator::ClaimFactory()
                    ->makeDefault()
                    ->setInvoiceId($linkingInvoice->getId())
                    ->setServiceId($service->getId())
                    ->setTariff($service->getCost())
                    ->setName(sprintf('Оплата %s кВт по счётчику "%s"', $delta, $counter->getNumber()))
                ;
            }
            else {
                $claim->setName(sprintf('Оплата %s кВт по счётчику "%s"', $delta, $counter->getNumber()));
            }


            $deltaMoney = MoneyService::parse($delta)->multiply($service->getCost());
            $hasChanges = $claim->getCost() !== MoneyService::toFloat($deltaMoney);
            $claim->setCost(MoneyService::toFloat($deltaMoney));
            $claim = ClaimLocator::ClaimService()->save($claim);

            if ($hasChanges) {
                $message = sprintf("%s автоматическа claim\n при показаниях счётчика \"%s\",\n участка \"%s\"\n на +%s кВт по тарифу %s",
                    $claimExists ? 'Обновлена' : 'Создана',
                    $counter->getNumber(),
                    $account->getNumber(),
                    $delta,
                    MoneyService::parse($service->getCost()),
                );

                HistoryChangesLocator::HistoryChangesService()->writeToHistory(
                    Event::COMMON,
                    HistoryType::INVOICE,
                    $linkingInvoice->getId(),
                    HistoryType::CLAIM,
                    $claim->getId(),
                    text: $message,
                );
            }

            if ( ! ClaimToObjectLocator::ClaimToObjectService()->hasRelations($claim)) {
                ClaimToObjectLocator::ClaimToObjectService()->create($claim, $history->getId(), ClaimObjectTypeEnum::COUNTER_HISTORY);
            }

            DB::commit();
        }
        catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    private function deleteClaim(?ClaimDTO $claim): void
    {
        if ($claim) {
            ClaimLocator::ClaimService()->deleteById($claim->getId());
        }
    }
}
