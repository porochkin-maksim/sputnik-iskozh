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
        $dirty    = false;
        foreach ($histories as $history) {
            $expectedPreviousId    = $previous?->getId();
            $expectedPreviousValue = $previous?->getValue();

            if ($history->getPreviousId() !== $expectedPreviousId || $history->getPreviousValue() !== $expectedPreviousValue) {
                $dirty = true;
            }

            if ($dirty) {
                if ($expectedPreviousId === $history->getId()) {
                    $expectedPreviousId = null;
                }

                $history->setPreviousId($expectedPreviousId);
                $history->setPreviousValue($expectedPreviousValue);
                $history  = $this->counterHistoryService->save($history);
                $events[] = new CounterHistoryConfirmed($history->getId());
            }

            $previous = $history;
        }

        if ($events) {
            $this->eventDispatcher->dispatch($events);
        }
    }
}
