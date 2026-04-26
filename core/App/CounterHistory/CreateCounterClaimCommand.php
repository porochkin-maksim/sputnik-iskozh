<?php declare(strict_types=1);

namespace Core\App\CounterHistory;

use Core\Domains\Billing\Jobs\CheckClaimForCounterChangeJob;
use Core\Domains\CounterHistory\CounterHistoryService;
use Illuminate\Support\Facades\DB;
use Throwable;

readonly class CreateCounterClaimCommand
{
    public function __construct(
        private CounterHistoryService $counterHistoryService,
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function execute(int $counterHistoryId): bool
    {
        DB::transaction(function () use ($counterHistoryId) {
            $history = $this->counterHistoryService->getById($counterHistoryId);
            if ($history !== null && ! $history->isVerified()) {
                $history->setIsVerified(true);
                $this->counterHistoryService->save($history);
            }

            dispatch_sync(new CheckClaimForCounterChangeJob($counterHistoryId));
        });

        return true;
    }
}
