<?php declare(strict_types=1);

namespace Core\App\HistoryChanges;

use Core\Domains\HistoryChanges\HistoryChangesService;

readonly class CreateHistoryHandler
{
    public function __construct(
        private HistoryChangesService $historyChangesService,
    )
    {
    }

    public function handle(CreateHistoryCommand $command): void
    {
        $this->historyChangesService->save($command->historyChanges);
    }
}
