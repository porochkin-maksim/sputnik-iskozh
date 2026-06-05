<?php declare(strict_types=1);

namespace Core\App\CounterHistory;

use Core\Domains\CounterHistory\CounterHistorySearcher;
use Core\Domains\CounterHistory\CounterHistoryService;
use Core\Contracts\DbServiceInterface;
use Throwable;

readonly class LinkCounterHistoryCommand
{
    public function __construct(
        private DbServiceInterface          $dbService,
        private CounterHistoryService       $counterHistoryService,
        private LinkCounterHistoryValidator $validator,
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function execute(int $historyId, int $counterId): void
    {
        $this->validator->validate($historyId, $counterId);

        $history = $this->counterHistoryService->getById($historyId);

        if ($history === null) {
            abort(404);
        }

        $this->dbService->transaction(function () use ($history, $counterId) {
            $counterHistory = $this->counterHistoryService->search(
                new CounterHistorySearcher()
                    ->setCounterId($counterId)
                    ->setWithCounter()
                    ->setWithPrevious()
                    ->setVerified(false)
                    ->defaultSort(),
            )->getItems()->last();

            if ($counterHistory !== null) {
                $history->setPreviousId($counterHistory->getId());
            }

            $history->setCounterId($counterId);
            $this->counterHistoryService->save($history);
        });
    }
}
