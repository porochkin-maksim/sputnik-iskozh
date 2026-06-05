<?php declare(strict_types=1);

namespace Core\App\Folders\Delete;

use Core\Domains\Files\FileSearcher;
use Core\Domains\Files\FileService;
use Core\Domains\Folders\FolderService;

readonly class FolderDeletionExecutor
{
    public function __construct(
        private FolderService $folderService,
        private FileService   $fileService,
    )
    {
    }

    /**
     * @param int[] $folderIds
     */
    public function execute(array $folderIds): bool
    {
        foreach ($folderIds as $folderId) {
            $this->deleteFolderFiles($folderId);
            $this->folderService->deleteDirectById($folderId);
        }

        return true;
    }

    private function deleteFolderFiles(int $folderId): void
    {
        $fileIds = $this->fileService->search(
            new FileSearcher()
                ->setType(null)
                ->setParentId($folderId),
        )->getIds();

        foreach ($fileIds as $fileId) {
            $this->fileService->deleteById($fileId);
        }
    }
}
