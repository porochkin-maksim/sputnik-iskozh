<?php declare(strict_types=1);

namespace Core\Domains\Folders\Services;

use Core\Domains\Files\Collections\FolderCollection;
use Core\Domains\Files\Factories\FolderFactory;
use Core\Domains\Files\Models\FileSearcher;
use Core\Domains\Files\Models\FolderDTO;
use Core\Domains\Files\Models\FolderSearcher;
use Core\Domains\Files\Responses\FolderSearchResponse;
use Core\Domains\Files\Services\FileService;
use Core\Domains\Folders\Repositories\FolderRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

readonly class FolderService
{
    public function __construct(
        private FolderRepository $folderRepository,
        private FolderFactory    $folderFactory,
        private FileService      $fileService,
    )
    {
    }

    public function save(FolderDTO $dto): FolderDTO
    {
        $folder = null;

        if ($dto->getId()) {
            $folder = $this->folderRepository->getById($dto->getId());
        }
        else {
            do {
                $uid    = Str::uuid()->getHex()->toString();
                $exists = $this->getByUid($uid);
                if ( ! $exists) {
                    $dto->setUid($uid);
                    break;
                }
            } while (true);
        }

        $folder = $this->folderFactory->makeModelFromDto($dto, $folder);
        $folder = $this->folderRepository->save($folder);

        return $this->folderFactory->makeDtoFromObject($folder);
    }

    public function search(FolderSearcher $searcher): FolderSearchResponse
    {
        $response = $this->folderRepository->search($searcher);

        $result = new FolderSearchResponse();
        $result->setTotal($response->getTotal());

        $collection = new FolderCollection();
        foreach ($response->getItems() as $item) {
            $collection->add($this->folderFactory->makeDtoFromObject($item));
        }

        return $result->setItems($collection);
    }

    public function getByUid(string $uid): ?FolderDTO
    {
        $searcher = new FolderSearcher();
        $searcher->setUid($uid);

        return $this->search($searcher)->getItems()->first();
    }

    public function getById(int $id): ?FolderDTO
    {
        $searcher = new FolderSearcher();
        $searcher->setId($id);

        return $this->search($searcher)->getItems()->first();
    }

    public function deleteById(int $id): bool
    {
        $folder = $this->getById($id);

        DB::beginTransaction();
        $result = false;
        try {
            if ($folder) {
                $searcher = new FolderSearcher();
                $searcher->setParentId($folder->getId());
                $folders = $this->search($searcher)->getItems();

                foreach ($folders as $f) {
                    $this->deleteById($f->getId());
                }

                $fileSearcher = new FileSearcher();
                $fileSearcher->setType(null)
                    ->setParentId($folder->getId());
                $fileIds = $this->fileService->search($fileSearcher)->getIds();
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
        $curFolder = $id ? $this->getById((int) $id) : null;
        if (!$curFolder) {
            return new FolderCollection();
        }

        $folderStack = [$curFolder];
        while ($curFolder->getParentId()) {
            $curFolder = $this->getById($curFolder->getParentId());
            if ($curFolder) {
                $folderStack[] = $curFolder;
            }
        }

        return new FolderCollection($folderStack);
    }
}
