<?php declare(strict_types=1);

namespace Core\App\CounterHistory;

use Core\Domains\Billing\Jobs\CheckClaimForCounterChangeJob;
use Core\Domains\CounterHistory\CounterHistorySearcher;
use Core\Domains\CounterHistory\CounterHistoryService;
use Illuminate\Support\Facades\DB;
use Throwable;

readonly class ConfirmCounterHistoriesCommand
{
    public function __construct(
        private CounterHistoryService $counterHistoryService,
        private ConfirmCounterHistoriesValidator $validator,
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function execute(array $ids): void
    {
        $this->validator->validate($ids);

        DB::transaction(function () use ($ids) {
            $counterHistories = $this->counterHistoryService->search(
                (new CounterHistorySearcher())
                    ->setIds($ids)
                    ->setVerified(false)
                    ->defaultSort(),
            )->getItems();

            foreach ($counterHistories as $history) {
                $history->setIsVerified(true);
                $history = $this->counterHistoryService->save($history);
                dispatch(new CheckClaimForCounterChangeJob($history->getId()));
            }
        });
    }
}
