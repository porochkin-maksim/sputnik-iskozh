<?php declare(strict_types=1);

namespace Core\App\Folders\Delete;

use Core\Domains\Folders\FolderSearcher;
use Core\Domains\Folders\FolderService;

readonly class FolderDeletionWalker
{
    public function __construct(
        private FolderService $folderService,
    )
    {
    }

    /**
     * @return int[]
     */
    public function collectFolderIds(int $folderId): array
    {
        $ids = [];
        $this->walk($folderId, $ids);

        return $ids;
    }

    /**
     * @param int[] $ids
     */
    private function walk(int $folderId, array &$ids): void
    {
        $folders = $this->folderService->search(
            new FolderSearcher()->setParentId($folderId),
        )->getItems();

        foreach ($folders as $nestedFolder) {
            $this->walk($nestedFolder->getId(), $ids);
        }

        $ids[] = $folderId;
    }
}
