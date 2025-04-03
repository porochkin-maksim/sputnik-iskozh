<?php declare(strict_types=1);

namespace Core\Domains\Counter\Jobs;

use Core\Domains\File\Enums\FileTypeEnum;
use Core\Domains\File\FileLocator;
use Core\Domains\File\Models\FileSearcher;
use Core\Queue\QueueEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteCounterHistoryFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $counterHistoryId,
    )
    {
        $this->onQueue(QueueEnum::DEFAULT->value);
    }

    public function handle(): void
    {
        $fileSearcher = new FileSearcher();
        $fileSearcher->setRelatedId($this->counterHistoryId)
            ->setType(FileTypeEnum::COUNTER)
        ;
        $fileService = FileLocator::FileService();
        foreach ($fileService->search($fileSearcher)->getItems() as $file) {
            $fileService->deleteById($file->getId());
        }
    }
}