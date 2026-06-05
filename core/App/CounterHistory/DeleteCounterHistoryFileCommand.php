<?php declare(strict_types=1);

namespace Core\App\CounterHistory;

use Core\Domains\Counter\FileService;

readonly class DeleteCounterHistoryFileCommand
{
    public function __construct(
        private FileService $fileService,
    )
    {
    }

    public function execute(int $counterHistoryId): void
    {
        $this->fileService->deleteHistoryById($counterHistoryId);
    }
}
