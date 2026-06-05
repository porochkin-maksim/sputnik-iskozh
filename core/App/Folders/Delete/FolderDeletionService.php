<?php declare(strict_types=1);

namespace Core\App\Folders\Delete;

use Core\Contracts\DbServiceInterface;
use Core\Domains\Folders\FolderService;

readonly class FolderDeletionService
{
    public function __construct(
        private FolderService          $folderService,
        private FolderDeletionWalker   $walker,
        private FolderDeletionExecutor $executor,
        private DbServiceInterface     $dbService,
    )
    {
    }

    public function deleteById(int $id): bool
    {
        $folder = $this->folderService->getById($id);
        if ( ! $folder) {
            return false;
        }

        return (bool) $this->dbService->transaction(function () use ($folder): bool {
            $folderIds = $this->walker->collectFolderIds($folder->getId());
            $this->executor->execute($folderIds);

            return true;
        });
    }
}
