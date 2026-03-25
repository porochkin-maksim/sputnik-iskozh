<?php declare(strict_types=1);

namespace Core\Domains\Billing\Jobs;

use App\Models\Billing\Claim;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Billing\Invoice\InvoiceLocator;
use Core\Domains\Billing\Invoice\Models\InvoiceSearcher;
use Core\Domains\Billing\Payment\Collections\PaymentCollection;
use Core\Domains\Billing\Service\Enums\ServiceTypeEnum;
use Core\Domains\Billing\Service\Models\ServiceSearcher;
use Core\Domains\Billing\Service\ServiceLocator;
use Core\Domains\Billing\Claim\Models\ClaimSearcher;
use Core\Domains\Billing\Claim\ClaimLocator;
use Core\Domains\Infra\DbLock\Enum\LockNameEnum;
use Core\Domains\Infra\DbLock\LockLocator;
use Core\Queue\QueueEnum;
use Core\Services\Money\MoneyService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Log;
use Throwable;

/**
 * Пересчитывает оплату claims в счёте по платежам
 */
class RecalcClaimsPaidJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Задержка между попытками (секунды)
     */
    public int $backoff = 10;

    public function __construct(
        private readonly int $invoiceId,
    )
    {
        $this->onQueue(QueueEnum::DEFAULT->value);
    }

    private static function getLockName(): LockNameEnum
    {
        return LockNameEnum::RECALC_CLAIMS_PAID_JOB;
    }

    /**
     * Диспатчит задачу только если она не была уже запущена для этого счёта
     */
    public static function dispatchIfNeeded(int $invoiceId, bool $sync = false): bool
    {
        $lockService = LockLocator::LockService();
        $lockName    = self::getLockName();

        // Проверяем доступность блокировки
        if ( ! $lockService->isAvailable($lockName, $invoiceId)) {
            return false;
        }

        // 1. Сначала создаём блокировку
        $lockService->lock($lockName, 60, $invoiceId);

        // 2. Диспатчим джобу
        if ($sync) {
            dispatch_sync(new self($invoiceId));
        }
        else {
            dispatch(new self($invoiceId)->onQueue(QueueEnum::DEFAULT->value));
        }

        return true;
    }

    public function handle(): void
    {
        try {
            $closure = fn() => $this->processRecalculation();

            DB::transaction($closure);
        }
        finally {
            $this->releaseLock();
        }
    }

    /**
     * Основная логика пересчёта
     */
    private function processRecalculation(): void
    {
        $searcher = new InvoiceSearcher();
        $searcher
            ->setId($this->invoiceId)
            ->setWithClaims()
            ->setWithPayments()
        ;

        $invoice = InvoiceLocator::InvoiceService()->search($searcher)->getItems()->first();

        if ( ! $invoice) {
            return;
        }

        $totalPaid = ($invoice->getPayments() ? : new PaymentCollection())
            ->getVerified()
            ->getTotalCostMoney()
        ;

        $sortedClaims = ClaimLocator::ClaimService()->search(new ClaimSearcher()
            ->setInvoiceId($invoice->getId())
            ->setWithService()
            ->setSortOrderProperty(Claim::SERVICE_ID, SearcherInterface::SORT_ORDER_ASC),
        )->getItems();
        $sortedClaims = $sortedClaims->sortByServiceTypes();
        $advanceClaim = $sortedClaims->getAdvancePayment();

        // Сбрасываем paid у всех
        foreach ($sortedClaims as $claim) {
            $claim->setPaid(0);
        }

        // Распределяем оплату
        $remaining = $totalPaid;
        foreach ($sortedClaims as $claim) {
            if ($claim->getId() === $advanceClaim?->getId()) {
                continue;
            }

            $claimCost = MoneyService::parse($claim->getCost());

            if ($remaining->subtract($claimCost)->isPositive()) {
                $claimPaid = $claimCost;
            }
            else {
                $claimPaid = $remaining;
            }

            $remaining = $remaining->subtract($claimPaid);
            $claim->setPaid(MoneyService::toFloat($claimPaid));

            if ($remaining->isZero()) {
                break;
            }
        }

        // Обновляем или создаём авансовый claim
        if ($remaining->isPositive()) {
            $service = ServiceLocator::ServiceService()->search(
                ServiceSearcher::make()
                    ->setPeriodId($invoice->getPeriodId())
                    ->setType(ServiceTypeEnum::ADVANCE_PAYMENT),
            )->getItems()->first();


            if ($service) {
                if ($advanceClaim) {
                    $advanceClaim
                        ->setTariff(MoneyService::toFloat($remaining))
                        ->setCost(MoneyService::toFloat($remaining))
                        ->setPaid(MoneyService::toFloat($remaining))
                        ->setName('Аванс')
                    ;
                }
                else {
                    $advanceClaim = ClaimLocator::ClaimFactory()->makeDefault()
                        ->setInvoiceId($invoice->getId())
                        ->setServiceId($service->getId())
                        ->setCost(MoneyService::toFloat($remaining))
                        ->setPaid(MoneyService::toFloat($remaining))
                        ->setName('Аванс')
                    ;
                    $sortedClaims->push($advanceClaim);
                }
            }
        }
        elseif ($advanceClaim && $advanceClaim->getPaid() > 0) {
            // Если остатка нет, но авансовый claim есть – удаляем его
            $sortedClaims->removeById($advanceClaim->getId());
            ClaimLocator::ClaimService()->deleteById($advanceClaim->getId());
        }

        // Сохраняем все claims
        $savedClaims = ClaimLocator::ClaimService()->saveCollection($sortedClaims);

        // Пересчитываем счёт
        $totalCost    = MoneyService::parse(0);
        $totalPaidSum = MoneyService::parse(0);
        foreach ($savedClaims as $claim) {
            $totalCost    = $totalCost->add(MoneyService::parse($claim->getCost()));
            $totalPaidSum = $totalPaidSum->add(MoneyService::parse($claim->getPaid()));
        }

        $invoice->setCost(MoneyService::toFloat($totalCost));
        $invoice->setPaid(MoneyService::toFloat($totalPaidSum));
        $invoice->setAdvance((float) $advanceClaim?->getCost());
        $invoice->setDebt((float) $sortedClaims->getDebt()?->getCost());

        InvoiceLocator::InvoiceService()->save($invoice);
    }

    /**
     * Освобождает блокировку
     */
    private function releaseLock(): void
    {
        try {
            LockLocator::LockService()->release(self::getLockName(), $this->invoiceId);
        }
        catch (Throwable $e) {
            Log::error('Failed to release lock', [
                'lock_name'  => self::getLockName()->value,
                'invoice_id' => $this->invoiceId,
                'error'      => $e->getMessage(),
            ]);
        }
    }

    /**
     * Обработчик ошибок (вызывается после всех попыток)
     */
    public function failed(Throwable $exception): void
    {
        $this->releaseLock();
    }
}