<?php declare(strict_types=1);

namespace App\Jobs\CounterHistory;

use App\Locators\FileLocator;
use App\Services\Queue\QueueEnum;
use Core\Domains\Files\FileSearcher;
use Core\Domains\Files\FileTypeEnum;
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
            ->setType(FileTypeEnum::COUNTER_HISTORY)
        ;

        $fileService = FileLocator::FileService();
        foreach ($fileService->search($fileSearcher)->getItems() as $file) {
            $fileService->deleteById($file->getId());
        }
    }
}
