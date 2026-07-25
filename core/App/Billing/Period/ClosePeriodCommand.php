<?php declare(strict_types=1);

namespace Core\App\Billing\Period;

use Carbon\Carbon;
use Core\App\Billing\Invoice\RecalcClaimsPaidCommand;
use Core\Domains\Billing\Claim\ClaimEntity;
use Core\Domains\Billing\Claim\ClaimFactory;
use Core\Domains\Billing\Claim\ClaimService;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Invoice\InvoiceFactory;
use Core\Domains\Billing\Invoice\InvoiceSearcher;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Invoice\InvoiceTypeEnum;
use Core\Domains\Billing\Period\PeriodEntity;
use Core\Domains\Billing\Period\PeriodService;
use Core\Domains\Billing\Service\ServiceCatalogService;
use Core\Domains\Billing\Service\ServiceTypeEnum;
use Core\Exceptions\ValidationException;
use Illuminate\Support\Facades\Log;

readonly class ClosePeriodCommand
{
    public function __construct(
        private PeriodService           $periodService,
        private InvoiceService          $invoiceService,
        private ClaimService            $claimService,
        private ClaimFactory            $claimFactory,
        private InvoiceFactory          $invoiceFactory,
        private ServiceCatalogService   $serviceService,
        private RecalcClaimsPaidCommand $recalcClaimsPaidCommand,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function execute(int $periodId, int $userId): void
    {
        $period = $this->periodService->getById($periodId);

        if ($period === null) {
            throw new ValidationException([], 'Период не найден');
        }

        if ($period->isClosed()) {
            throw new ValidationException([], 'Период уже закрыт');
        }

        $period
            ->setIsClosed(true)
            ->setClosedAt(Carbon::now())
            ->setClosedBy($userId)
        ;

        $this->periodService->save($period);

        $nextPeriod = $this->periodService->getOpenPeriodsAsc()->first();

        $invoices = $this->invoiceService->search(
            InvoiceSearcher::make()
                ->setPeriodId($periodId)
                ->setWithAccount(),
        )->getItems();

        $accountIds = [];
        foreach ($invoices as $invoice) {
            $accountId = $invoice->getAccountId();
            $accountIds[$accountId] = $accountId;

            if ($nextPeriod && $invoice->getType() !== InvoiceTypeEnum::OUTCOME) {
                $this->migrateUnpaidClaimsToNextPeriod($invoice, $nextPeriod, $period->getName());
            }
        }

        foreach ($accountIds as $accountId) {
            $this->recalcClaimsPaidCommand->executeForAccount($accountId);
        }
    }

    private function migrateUnpaidClaimsToNextPeriod(
        InvoiceEntity $closedInvoice,
        PeriodEntity  $nextPeriod,
        string        $closedPeriodName,
    ): void
    {
        $claims = $this->claimService->getByInvoiceIdSorted($closedInvoice->getId())
            ->filter(static fn(ClaimEntity $c) => ($c->getDelta() ?? 0.0) > 0)
        ;

        if ($claims->isEmpty()) {
            return;
        }

        $nextInvoice = $this->invoiceService->search(
            InvoiceSearcher::make()
                ->setPeriodId($nextPeriod->getId())
                ->setAccountId($closedInvoice->getAccountId())
                ->setType($closedInvoice->getType())
                ->setLimit(1),
        )->getItems()->first();

        if ($nextInvoice === null) {
            if ($closedInvoice->getType() !== InvoiceTypeEnum::REGULAR) {
                return;
            }

            $nextInvoice = $this->invoiceFactory->makeDefault()
                ->setAccountId($closedInvoice->getAccountId())
                ->setPeriodId($nextPeriod->getId())
                ->setType(InvoiceTypeEnum::REGULAR);

            $nextInvoice = $this->invoiceService->save($nextInvoice);
        }

        $nextDebtService = $this->serviceService->getByPeriodIdAndType(
            $nextPeriod->getId(),
            ServiceTypeEnum::DEBT,
        );

        if ($nextDebtService === null) {
            Log::warning('Нет услуги DEBT в следующем периоде для миграции долгов', [
                'account_id' => $closedInvoice->getAccountId(),
                'period_id'  => $nextPeriod->getId(),
            ]);
            return;
        }

        foreach ($claims as $claim) {
            $delta          = $claim->getDelta();
            $tariff         = $claim->getTariff();
            $quantity       = ($tariff && $tariff > 0) ? round($delta / $tariff, 2) : 0;
            $oldService     = $claim->getService();
            $newServiceName = $claim->getOriginalClaimId() !== null
                ? ($claim->getName() ?: $oldService?->getName())
                : sprintf(
                    '%s (долг за период %s)',
                    $oldService?->getName() ?: $oldService?->getType()?->name() ?: 'Услуга',
                    $closedPeriodName,
                );

            $debtClaim = $this->claimFactory->makeDefault()
                ->setQuantity($quantity)
                ->setInvoiceId($nextInvoice->getId())
                ->setServiceId($nextDebtService->getId())
                ->setOriginalServiceId($claim->getServiceId())
                ->setOriginalClaimId($claim->getId())
                ->setTariff($claim->getTariff())
                ->setCost($delta)
                ->setName($newServiceName)
            ;

            $this->claimService->save($debtClaim);
        }
    }
}
