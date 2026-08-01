<?php declare(strict_types=1);

namespace App\Console\Commands\Billing;

use App\Models\Infra\HistoryChanges;
use Core\Domains\Billing\Claim\ClaimService;
use Core\Domains\Billing\Payment\PaymentService;
use Core\Domains\CounterHistory\CounterHistoryService;
use Core\Domains\HistoryChanges\HistoryChangesSearcher;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Core\Domains\HistoryChanges\HistoryType;
use Illuminate\Console\Command;

class SyncHistoryPrimaryIds extends Command
{
    protected $signature   = 'history:sync-primary-ids';
    protected $description = 'Синхронизирует primaryId для записей истории с referenceId, но без primaryId';

    private const array REFERENCE_MAP = [
        HistoryType::PAYMENT->value         => 'resolvePaymentPrimaryId',
        HistoryType::CLAIM->value           => 'resolveClaimPrimaryId',
        HistoryType::COUNTER_HISTORY->value => 'resolveCounterHistoryPrimaryId',
    ];

    public function __construct(
        private readonly HistoryChangesService $historyChangesService,
        private readonly PaymentService        $paymentService,
        private readonly ClaimService          $claimService,
        private readonly CounterHistoryService $counterHistoryService,
    )
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $searcher = new HistoryChangesSearcher()
            ->setPrimaryIdIsNull()
            ->setLimit(1000)
            ->setSortOrderProperty(HistoryChanges::ID, 'asc')
        ;

        $records = $this->historyChangesService->search($searcher)->getItems();

        if ($records->isEmpty()) {
            $this->info('Нет записей без primaryId.');

            return;
        }

        $fixed = 0;

        foreach ($records as $record) {
            $referenceType = $record->getReferenceType();
            $referenceId   = $record->getReferenceId();

            if ($referenceType === null || $referenceId === null) {
                continue;
            }

            $method = self::REFERENCE_MAP[$referenceType->value] ?? null;

            if ($method === null) {
                continue;
            }

            $primaryId = $this->{$method}($referenceId);

            if ($primaryId) {
                $record->setPrimaryId($primaryId);
                $record->setType(HistoryType::INVOICE);
                $this->historyChangesService->save($record);
                $fixed++;
            }
        }

        $this->info("Обработано: {$fixed} записей.");
    }

    private function resolvePaymentPrimaryId(int $referenceId): ?int
    {
        return $this->paymentService->getById($referenceId)?->getInvoiceId();
    }

    private function resolveClaimPrimaryId(int $referenceId): ?int
    {
        return $this->claimService->getById($referenceId)?->getInvoiceId();
    }

    private function resolveCounterHistoryPrimaryId(int $referenceId): ?int
    {
        return $this->counterHistoryService->getById($referenceId)?->getCounterId();
    }
}
