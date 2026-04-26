<?php declare(strict_types=1);

namespace Core\Domains\Folders;

use Core\Domains\Files\FileSearcher;
use Core\Domains\Files\FileService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

readonly class FolderService
{
    public function __construct(
        private FolderRepositoryInterface $folderRepository,
        private FileService               $fileService,
    )
    {
    }

    public function save(FolderEntity $folder): FolderEntity
    {
        if ( ! $folder->getId()) {
            do {
                $uid = Str::uuid()->getHex()->toString();
            } while ($this->getByUid($uid));

            $folder->setUid($uid);
        }

        return $this->folderRepository->save($folder);
    }

    public function search(FolderSearcher $searcher): FolderSearchResponse
    {
        return $this->folderRepository->search($searcher);
    }

    public function getByUid(string $uid): ?FolderEntity
    {
        return $this->search(
            new FolderSearcher()
                ->setUid($uid)
                ->setLimit(1),
        )->getItems()->first();
    }

    public function getById(?int $id): ?FolderEntity
    {
        return $this->folderRepository->getById($id);
    }

    public function deleteById(int $id): bool
    {
        $folder = $this->getById($id);

        DB::beginTransaction();
        $result = false;
        try {
            if ($folder) {
                $folders = $this->search(
                    new FolderSearcher()->setParentId($folder->getId()),
                )->getItems();

                foreach ($folders as $nestedFolder) {
                    $this->deleteById($nestedFolder->getId());
                }

                $fileIds = $this->fileService->search(new FileSearcher()
                        ->setType(null)
                        ->setParentId($folder->getId()),
                )->getIds();

                foreach ($fileIds as $fileId) {
                    $this->fileService->deleteById($fileId);
                }

                $result = $this->folderRepository->deleteById($folder->getId());
            }
            DB::commit();
        }
        catch (\Exception) {
            DB::rollBack();
        }

        return $result;
    }

    public function getWithParentsRecursively(int|string|null $id): FolderCollection
    {
        $currentFolder = $id ? $this->getById((int) $id) : null;
        if ( ! $currentFolder) {
            return new FolderCollection();
        }

        $folderStack = [$currentFolder];
        while ($currentFolder->getParentId()) {
            $currentFolder = $this->getById($currentFolder->getParentId());
            if ($currentFolder) {
                $folderStack[] = $currentFolder;
            }
        }

        return new FolderCollection($folderStack);
    }
}
