<?php declare(strict_types=1);

namespace Core\App\HistoryChanges;

use Core\Domains\HistoryChanges\HistoryChangesService;

readonly class CreateHistoryCommand
{
    public function __construct(
        private HistoryChangesService $historyChangesService,
    )
    {
    }

    public function execute(CreateHistoryInput $input): void
    {
        $this->historyChangesService->save($input->historyChanges);
    }
}
