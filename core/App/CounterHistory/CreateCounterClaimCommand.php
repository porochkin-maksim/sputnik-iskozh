<?php declare(strict_types=1);

namespace Core\App\CounterHistory;

use Core\Contracts\EventDispatcherInterface;
use Core\Contracts\DbServiceInterface;
use Core\Domains\CounterHistory\CounterHistoryService;
use Core\Domains\CounterHistory\Events\CounterHistoryConfirmed;
use Throwable;

readonly class CreateCounterClaimCommand
{
    public function __construct(
        private DbServiceInterface       $dbService,
        private CounterHistoryService    $counterHistoryService,
        private EventDispatcherInterface $eventDispatcher,
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function execute(int $counterHistoryId): bool
    {
        $this->dbService->transaction(function () use ($counterHistoryId) {
            $history = $this->counterHistoryService->getById($counterHistoryId);
            if ($history !== null) {
                $history->setIsVerified(true);
                $this->counterHistoryService->save($history);
            }
        });

        $this->eventDispatcher->dispatch(new CounterHistoryConfirmed($counterHistoryId));

        return true;
    }
}
