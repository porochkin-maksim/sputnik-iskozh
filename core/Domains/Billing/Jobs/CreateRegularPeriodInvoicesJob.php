<?php declare(strict_types=1);

namespace Core\Domains\Billing\Jobs;

use Core\Domains\Billing\Invoice\Enums\InvoiceTypeEnum;
use Core\Domains\Billing\Invoice\InvoiceLocator;
use Core\Domains\Billing\Period\PeriodLocator;
use Core\Domains\Infra\DbLock\Enum\LockNameEnum;
use Core\Domains\Infra\DbLock\LockLocator;
use Core\Queue\QueueEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

/**
 * По команде создаёт регулярные счета для всех участков, у которых их ещё нет
 */
class CreateRegularPeriodInvoicesJob implements ShouldQueue
{
    private const int LIMIT = 50;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int   $periodId,
        private readonly array $acountIds = [],
    )
    {
        $this->onQueue(QueueEnum::DEFAULT->value);
    }

    private static function getLockName(): LockNameEnum
    {
        return LockNameEnum::CREATE_REGULAR_PERIOD_INVOICES_JOB;
    }

    /**
     * Диспатчит задачу только если она не была уже запущена для этого счёта
     */
    public static function dispatchIfNeeded(int $periodId, bool $sync = false): bool
    {
        $lockService = LockLocator::LockService();
        $lockName    = self::getLockName();

        // Проверяем доступность блокировки
        if ( ! $lockService->isAvailable($lockName, $periodId)) {
            return false;
        }

        // 1. Сначала создаём блокировку
        $lockService->lock($lockName, 120, $periodId);

        // 2. Диспатчим джобу
        if ($sync) {
            dispatch_sync(new self($periodId));
        }
        else {
            dispatch(new self($periodId)->onQueue(QueueEnum::DEFAULT->value));
        }

        return true;
    }

    public function handle(): void
    {
        try {
            $closure = fn() => $this->processCreatingInvoices();

            DB::transaction($closure);
        }
        finally {
            $this->releaseLock();
        }
    }

    public function processCreatingInvoices(): void
    {
        if ( ! PeriodLocator::PeriodService()->getById($this->periodId)) {
            throw new RuntimeException("Период не найден #{$this->periodId}");
        }

        if ( ! $this->acountIds) {
            $accountIds = InvoiceLocator::InvoiceService()->getAccountsWithoutRegularInvoice($this->periodId)->getIds();
            if ( ! $accountIds) {
                return;
            }
            foreach (array_chunk($accountIds, self::LIMIT) as $accountIdsChunk) {
                dispatch(new self($this->periodId, $accountIdsChunk));
            }

            return;
        }

        foreach ($this->acountIds as $acountId) {
            $invoice = InvoiceLocator::InvoiceFactory()->makeDefault()
                ->setType(InvoiceTypeEnum::REGULAR)
                ->setPeriodId($this->periodId)
                ->setAccountId($acountId)
            ;

            InvoiceLocator::InvoiceService()->save($invoice);
        }
    }

    /**
     * Освобождает блокировку
     */
    private function releaseLock(): void
    {
        try {
            LockLocator::LockService()->release(self::getLockName(), $this->periodId);
        }
        catch (Throwable $e) {
            Log::error('Failed to release lock', [
                'lock_name' => self::getLockName()->value,
                'period_id' => $this->periodId,
                'error'     => $e->getMessage(),
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