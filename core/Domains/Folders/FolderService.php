<?php declare(strict_types=1);

namespace Core\Domains\Folders;

use Core\Contracts\StringServiceInterface;

readonly class FolderService
{
    public function __construct(
        private StringServiceInterface    $stringService,
        private FolderRepositoryInterface $folderRepository,
    )
    {
    }

    public function save(FolderEntity $folder): FolderEntity
    {
        if ( ! $folder->getId()) {
            do {
                $uid = $this->stringService->uuidHex();
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

    public function deleteDirectById(int $id): bool
    {
        return $this->folderRepository->deleteById($id);
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
