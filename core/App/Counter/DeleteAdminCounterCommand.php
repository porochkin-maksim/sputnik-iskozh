<?php declare(strict_types=1);

namespace Core\App\Counter;

use Core\Domains\Counter\CounterService;
use Core\Domains\HistoryChanges\Event;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Core\Domains\HistoryChanges\HistoryType;
use Core\Contracts\DbServiceInterface;
use Throwable;

readonly class DeleteAdminCounterCommand
{
    public function __construct(
        private DbServiceInterface    $dbService,
        private CounterService        $counterService,
        private HistoryChangesService $historyChangesService,
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function execute(int $counterId, ?string $comment): bool
    {
        return $this->dbService->transaction(function () use ($counterId, $comment) {
            $result = $this->counterService->deleteById($counterId);

            if ($result && $comment) {
                $this->historyChangesService->writeToHistory(
                    Event::DELETE,
                    HistoryType::COUNTER,
                    $counterId,
                    text: sprintf('Удалён по причине: %s', $comment),
                );
            }

            return $result;
        });
    }
}
