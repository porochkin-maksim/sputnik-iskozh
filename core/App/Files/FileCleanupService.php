<?php declare(strict_types=1);

namespace Core\App\Files;

use Core\Domains\Files\FileSearcher;
use Core\Domains\Files\FileService;
use Core\Domains\Files\FileTypeEnum;

readonly class FileCleanupService
{
    public function __construct(
        private FileService $fileService,
    )
    {
    }

    public function deleteByTypeAndRelatedId(FileTypeEnum $type, int $relatedId): void
    {
        $files = $this->fileService->search(new FileSearcher()
            ->setRelatedId($relatedId)
            ->setType($type),
        )->getItems();

        foreach ($files as $file) {
            $this->fileService->deleteById($file->getId());
        }
    }
}
