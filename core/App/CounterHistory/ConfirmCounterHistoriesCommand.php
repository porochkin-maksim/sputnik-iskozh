<?php declare(strict_types=1);

namespace Core\App\CounterHistory;

use Core\Contracts\EventDispatcherInterface;
use Core\Domains\CounterHistory\Events\CounterHistoryConfirmed;
use Core\Domains\CounterHistory\CounterHistorySearcher;
use Core\Domains\CounterHistory\CounterHistoryService;
use Illuminate\Support\Facades\DB;
use Throwable;

readonly class ConfirmCounterHistoriesCommand
{
    public function __construct(
        private CounterHistoryService            $counterHistoryService,
        private ConfirmCounterHistoriesValidator $validator,
        private EventDispatcherInterface         $eventDispatcher,
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function execute(array $ids): void
    {
        $this->validator->validate($ids);
        $events = [];

        DB::transaction(function () use ($ids, &$events) {
            $counterHistories = $this->counterHistoryService->search(
                new CounterHistorySearcher()
                    ->setIds($ids)
                    ->setVerified(false)
                    ->defaultSort(),
            )->getItems();

            foreach ($counterHistories as $history) {
                $history->setIsVerified(true);
                $history  = $this->counterHistoryService->save($history);
                $events[] = new CounterHistoryConfirmed($history->getId());
            }
        });

        if ($events) {
            $this->eventDispatcher->dispatch($events);
        }
    }
}
