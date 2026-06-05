<?php declare(strict_types=1);

namespace Core\App\CounterHistory;

use Core\Contracts\EventDispatcherInterface;
use Core\Domains\CounterHistory\CounterHistorySearcher;
use Core\Domains\CounterHistory\CounterHistoryService;
use Core\Domains\CounterHistory\Events\CounterHistoryConfirmed;

readonly class RewatchCounterHistoryChainCommand
{
    public function __construct(
        private CounterHistoryService    $counterHistoryService,
        private EventDispatcherInterface $eventDispatcher,
    )
    {
    }

    public function execute(RewatchCounterHistoryChainInput $input): void
    {
        $counterHistorySearcher = new CounterHistorySearcher();
        $counterHistorySearcher
            ->setCounterId($input->counterId)
            ->defaultSort()
        ;

        $histories = $this->counterHistoryService->search($counterHistorySearcher)->getItems();

        $events   = [];
        $previous = null;
        foreach ($histories as $history) {
            if ( ! $previous) {
                $history->setPreviousId(null);
                $history->setPreviousValue(null);
            }
            else {
                $history->setPreviousId($previous->getId());
                $history->setPreviousValue($previous->getValue());
            }

            $history  = $this->counterHistoryService->save($history);
            $events[] = new CounterHistoryConfirmed($history->getId());
            $previous = $history;
        }

        if ($events) {
            $this->eventDispatcher->dispatch($events);
        }
    }
}
