<?php declare(strict_types=1);

namespace Core\Domains\File\Jobs;

use Core\Domains\File\Enums\FileTypeEnum;
use Core\Domains\File\FileLocator;
use Core\Domains\File\Models\FileSearcher;
use Core\Domains\Infra\DbLock\Enum\LockNameEnum;
use Core\Queue\DispatchIfNeededTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteFilesJob implements ShouldQueue
{
    use DispatchIfNeededTrait;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int          $relatedId,
        private readonly FileTypeEnum $type,
    )
    {
    }

    protected function getIdentificator(): null|int|string
    {
        return $this->type->value . '-' . $this->relatedId;
    }

    protected static function getLockName(): LockNameEnum
    {
        return LockNameEnum::DELETE_FILES_JOB;
    }

    protected function process(): void
    {
        $fileService = FileLocator::FileService();

        $files = $fileService->search(new FileSearcher()
            ->setRelatedId($this->relatedId)
            ->setType($this->type),
        )->getItems();

        foreach ($files as $file) {
            $fileService->deleteById($file->getId());
        }
    }
}
