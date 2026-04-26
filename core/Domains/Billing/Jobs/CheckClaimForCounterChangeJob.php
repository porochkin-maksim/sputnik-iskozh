<?php declare(strict_types=1);

namespace Core\Domains\Billing\Jobs;

use App\Models\Billing\Invoice;
use App\Models\Billing\Service;
use App\Models\Counter\Counter;
use App\Services\Money\MoneyService;
use App\Services\Queue\DispatchIfNeededTrait;
use App\Services\Queue\QueueEnum;
use Core\Domains\Account\AccountService;
use Core\Domains\Billing\Claim\ClaimEntity;
use Core\Domains\Billing\Claim\ClaimFactory;
use Core\Domains\Billing\Claim\ClaimService;
use Core\Domains\Billing\ClaimToObject\ClaimObjectTypeEnum;
use Core\Domains\Billing\ClaimToObject\ClaimToObjectService;
use Core\Domains\Billing\Invoice\InvoiceFactory;
use Core\Domains\Billing\Invoice\InvoiceSearcher;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Invoice\InvoiceTypeEnum;
use Core\Domains\Billing\Period\PeriodService;
use Core\Domains\Billing\Service\ServiceSearcher;
use Core\Domains\Billing\Service\ServiceCatalogService;
use Core\Domains\Billing\Service\ServiceTypeEnum;
use Core\Domains\Counter\CounterService;
use Core\Domains\Counter\CounterSearcher;
use Core\Domains\CounterHistory\CounterHistoryService;
use Core\Domains\HistoryChanges\Event;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Core\Domains\HistoryChanges\HistoryType;
use Core\Domains\Infra\DbLock\Enum\LockNameEnum;
use Core\Repositories\SearcherInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Процедура проверяет изменились ли показания привязанного счетчика и пересчитывает стоимость и тариф по текущему курсу
 */
class CheckClaimForCounterChangeJob implements ShouldQueue
{
    use DispatchIfNeededTrait;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $counterHistoryId,
    )
    {
        $this->onQueue(QueueEnum::DEFAULT->value);
    }

    protected static function getLockName(): LockNameEnum
    {
        return LockNameEnum::CHECK_CLAIM_FOR_COUNTER_CHANGE_JOB;
    }

    protected function getIdentificator(): null|int|string
    {
        return $this->counterHistoryId;
    }

    public function process(
        ClaimToObjectService $claimToObjectService,
        CounterHistoryService $counterHistoryService,
        CounterService $counterService,
        PeriodService $periodService,
        ServiceCatalogService $serviceService,
        AccountService $accountService,
        InvoiceService $invoiceService,
        InvoiceFactory $invoiceFactory,
        ClaimFactory $claimFactory,
        ClaimService $claimService,
        HistoryChangesService $historyChangesService,
    ): void
    {
        $claim = $claimToObjectService
            ->getByReference(ClaimObjectTypeEnum::COUNTER_HISTORY, $this->counterHistoryId)
        ;

        $history = $counterHistoryService->getById($this->counterHistoryId);
        if ( ! $history) {
            $this->deleteClaim($claim, $claimService);

            return;
        }

        $counterSearcher = new CounterSearcher();
        $counterSearcher
            ->setId($history->getCounterId())
            ->addWhere(Counter::IS_INVOICING, SearcherInterface::EQUALS, true)
        ;
        $counter = $counterService->search($counterSearcher)->getItems()->first();

        if ( ! $counter) {
            $this->deleteClaim($claim, $claimService);

            return;
        }

        $previous = $counterHistoryService->getPrevious($history);

        if ( ! $previous || ! $previous->isVerified()) {
            $this->deleteClaim($claim, $claimService);

            return;
        }

        $delta = $history->getValue() - $previous->getValue();

        if ((float) $delta <= 0) {
            $this->deleteClaim($claim, $claimService);

            return;
        }

        $period = $periodService->getActive();

        if ( ! $period) {
            $this->deleteClaim($claim, $claimService);

            return;
        }

        $serviceSearcher = new ServiceSearcher();
        $serviceSearcher
            ->setPeriodId($period->getId())
            ->addWhere(Service::TYPE, SearcherInterface::EQUALS, ServiceTypeEnum::ELECTRIC_TARIFF->value)
        ;
        $service = $serviceService->search($serviceSearcher)->getItems()->first();

        if ( ! $service || ! $service->getCost()) {
            $this->deleteClaim($claim, $claimService);

            return;
        }

        $account = $accountService->getById($counter->getAccountId());

        if ( ! $account || $account->isSnt()) {
            $this->deleteClaim($claim, $claimService);

            return;
        }

        $invoiceSearcher = new InvoiceSearcher();
        $invoiceSearcher
            ->setPeriodId($period->getId())
            ->setAccountId($account->getId())
            ->setSortOrderProperty(Invoice::ID, SearcherInterface::SORT_ORDER_DESC)
        ;
        $invoices = $invoiceService->search($invoiceSearcher)->getItems();

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

        $claimExists = (bool) $claim;

        if ( ! $claimExists) {
            if ( ! $linkingInvoice) {
                $linkingInvoice = $invoiceFactory
                    ->makeDefault()
                    ->setType($account->isSnt() ? InvoiceTypeEnum::OUTCOME : InvoiceTypeEnum::INCOME)
                    ->setPeriodId($period->getId())
                    ->setAccountId($account->getId())
                ;
                $linkingInvoice = $invoiceService->save($linkingInvoice);
            }

            $claim = $claimFactory
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
        $claim = $claimService->save($claim);

        if ($hasChanges) {
            $message = sprintf("%s автоматическа claim\n при показаниях счётчика \"%s\",\n участка \"%s\"\n на +%s кВт по тарифу %s",
                $claimExists ? 'Обновлена' : 'Создана',
                $counter->getNumber(),
                $account->getNumber(),
                $delta,
                MoneyService::parse($service->getCost()),
            );

            $historyChangesService->writeToHistory(
                Event::COMMON,
                HistoryType::INVOICE,
                $linkingInvoice->getId(),
                HistoryType::CLAIM,
                $claim->getId(),
                text: $message,
            );
        }

        if ( ! $claimToObjectService->hasRelations($claim)) {
            $claimToObjectService->create($claim, $history->getId(), ClaimObjectTypeEnum::COUNTER_HISTORY);
        }
    }

    private function deleteClaim(?ClaimEntity $claim, ClaimService $claimService): void
    {
        if ($claim) {
            $claimService->deleteById($claim->getId());
        }
    }
}
