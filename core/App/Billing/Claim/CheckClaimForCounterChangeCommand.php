<?php declare(strict_types=1);

namespace Core\App\Billing\Claim;

use App\Models\Billing\Invoice;
use App\Models\Counter\Counter;
use App\Services\Money\MoneyService;
use Carbon\Carbon;
use Core\Domains\Account\AccountService;
use Core\Domains\Billing\Claim\ClaimEntity;
use Core\Domains\Billing\Claim\ClaimFactory;
use Core\Domains\Billing\Claim\ClaimService;
use Core\Domains\Billing\ClaimToObject\ClaimObjectTypeEnum;
use Core\Domains\Billing\ClaimToObject\ClaimToObjectService;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Invoice\InvoiceFactory;
use Core\Domains\Billing\Invoice\InvoiceSearcher;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Invoice\InvoiceTypeEnum;
use Core\Domains\Billing\Period\PeriodService;
use Core\Domains\Billing\Service\ServiceCatalogService;
use Core\Domains\Billing\Service\ServiceSearcher;
use Core\Domains\Billing\Service\ServiceTypeEnum;
use Core\Domains\Counter\CounterSearcher;
use Core\Domains\Counter\CounterService;
use Core\Domains\CounterHistory\CounterHistoryService;
use Core\Domains\HistoryChanges\Event;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Core\Domains\HistoryChanges\HistoryType;
use Core\Repositories\SearcherInterface;

readonly class CheckClaimForCounterChangeCommand
{
    public function __construct(
        private ClaimToObjectService  $claimToObjectService,
        private CounterHistoryService $counterHistoryService,
        private CounterService        $counterService,
        private PeriodService         $periodService,
        private ServiceCatalogService $serviceService,
        private AccountService        $accountService,
        private InvoiceService        $invoiceService,
        private InvoiceFactory        $invoiceFactory,
        private ClaimFactory          $claimFactory,
        private ClaimService          $claimService,
        private HistoryChangesService $historyChangesService,
    )
    {
    }

    public function execute(CheckClaimForCounterChangeInput $input): void
    {
        $claim = $this->claimToObjectService
            ->getByReference(ClaimObjectTypeEnum::COUNTER_HISTORY, $input->counterHistoryId)
        ;

        $history = $this->counterHistoryService->getById($input->counterHistoryId);
        if ( ! $history) {
            $this->deleteClaim($claim);

            return;
        }

        $counterSearcher = new CounterSearcher();
        $counterSearcher
            ->setId($history->getCounterId())
            ->addWhere(Counter::IS_INVOICING, SearcherInterface::EQUALS, true)
        ;
        $counter = $this->counterService->search($counterSearcher)->getItems()->first();

        if ( ! $counter) {
            $this->deleteClaim($claim);

            return;
        }

        $previous = $this->counterHistoryService->getPrevious($history);

        if ( ! $previous || ! $previous->isVerified()) {
            $this->deleteClaim($claim);

            return;
        }

        $delta = $history->getValue() - $previous->getValue();

        if ((float) $delta <= 0) {
            $this->deleteClaim($claim);

            return;
        }

        $period = $this->periodService->getActive();

        if ( ! $period) {
            $this->deleteClaim($claim);

            return;
        }

        $serviceSearcher = new ServiceSearcher();
        $serviceSearcher
            ->setPeriodId($period->getId())
            ->setActiveAt(Carbon::now())
            ->setType(ServiceTypeEnum::ELECTRIC_TARIFF)
        ;
        $service = $this->serviceService->search($serviceSearcher)->getItems()->first();

        if ( ! $service || ! $service->getCost()) {
            $this->deleteClaim($claim);

            return;
        }

        $account = $this->accountService->getById($counter->getAccountId());

        if ( ! $account || $account->isSnt()) {
            $this->deleteClaim($claim);

            return;
        }

        $linkingInvoice = $this->getLinkingInvoice($period->getId(), $account->getId(), $service->getId());

        $claimExists = (bool) $claim;

        if ( ! $claimExists) {
            if ( ! $linkingInvoice) {
                $linkingInvoice = $this->invoiceFactory
                    ->makeDefault()
                    ->setType($account->isSnt() ? InvoiceTypeEnum::OUTCOME : InvoiceTypeEnum::INCOME)
                    ->setPeriodId($period->getId())
                    ->setAccountId($account->getId())
                    ->setServiceId($service->getId())
                    ->setName($service->getName())
                ;
                $linkingInvoice = $this->invoiceService->save($linkingInvoice);
            }

            $claim = $this->claimFactory
                ->makeDefault()
                ->setInvoiceId($linkingInvoice->getId())
                ->setServiceId($service->getId())
                ->setTariff($service->getCost())
                ->setQuantity($delta)
                ->setName(sprintf('Оплата %s кВт по счётчику "%s"', $delta, $counter->getNumber()))
            ;
        }
        else {
            $claim
                ->setQuantity($delta)
                ->setName(sprintf('Оплата %s кВт по счётчику "%s"', $delta, $counter->getNumber()));
        }

        $deltaMoney = MoneyService::parse($delta)->multiply($service->getCost());
        $hasChanges = $claim->getCost() !== MoneyService::toFloat($deltaMoney);
        $claim->setCost(MoneyService::toFloat($deltaMoney));
        $claim = $this->claimService->save($claim);

        $this->invoiceService->recalcInvoice($linkingInvoice->getId(), true);

        if ($hasChanges) {
            $message = sprintf(
                "%s автоматическая услуга\n при показаниях счётчика \"%s\",\n участка \"%s\"\n на +%s кВт по тарифу %s",
                $claimExists ? 'Обновлена' : 'Создана',
                $counter->getNumber(),
                $account->getNumber(),
                $delta,
                MoneyService::parse($service->getCost()),
            );

            $this->historyChangesService->writeToHistory(
                Event::COMMON,
                HistoryType::INVOICE,
                $linkingInvoice->getId(),
                HistoryType::CLAIM,
                $claim->getId(),
                text: $message,
            );
        }

        if ( ! $this->claimToObjectService->hasRelations($claim)) {
            $this->claimToObjectService->create($claim, $history->getId(), ClaimObjectTypeEnum::COUNTER_HISTORY);
        }
    }

    private function deleteClaim(?ClaimEntity $claim): void
    {
        if ($claim) {
            $invoiceId = $claim->getInvoiceId();
            $this->claimService->deleteById($claim->getId());
            if ($invoiceId) {
                $this->invoiceService->recalcInvoice($invoiceId, true);
            }
        }
    }

    private function getLinkingInvoice(int $periodId, int $accountId, int $serviceId): ?InvoiceEntity
    {
        $invoiceSearcher = new InvoiceSearcher()
            ->setPeriodId($periodId)
            ->setAccountId($accountId)
            ->setServiceId($serviceId)
            ->setType(InvoiceTypeEnum::INCOME)
            ->setSortOrderProperty(Invoice::ID, SearcherInterface::SORT_ORDER_DESC)
            ->setLimit(1)
        ;

        return $this->invoiceService->search($invoiceSearcher)->getItems()->first();
    }
}
