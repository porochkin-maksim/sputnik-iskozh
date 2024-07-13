<?php declare(strict_types=1);

namespace Core\Domains\File\Services;

use App\Models\File\File;
use Core\Domains\File\Collections\Files;
use Core\Domains\File\Factories\FileFactory;
use Core\Domains\File\Models\FileDTO;
use Core\Domains\File\Models\FileSearcher;
use Core\Domains\File\Repositories\FileRepository;
use Core\Domains\File\Responses\FileSearchResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

readonly class FileService
{
    public function __construct(
        private FileRepository $fileRepository,
        private FileFactory    $fileFactory,
    )
    {
    }

    public function store(
        UploadedFile $file,
        string       $directory = '',
        bool         $public = true,
    ): FileDTO
    {
        $baseDir = $public ? 'public/' : '';
        $path    = $file->storePublicly(sprintf('%s%s', $baseDir, $directory));

        $dto = new FileDto();
        $dto
            ->setName($file->getClientOriginalName())
            ->setExt($file->getClientOriginalExtension())
            ->setPath($path);

        return $dto;
    }

    public function removeFromStore(string $path): bool
    {
        return Storage::delete($path);
    }

    public function save(FileDTO $dto): FileDTO
    {
        $file = null;

        if ($dto->getId()) {
            $file = $this->fileRepository->getById($dto->getId());
        }
        else {
            $searcher = new FileSearcher();
            $searcher->setType($dto->getType())
                ->setSortOrderProperty(File::ORDER)
                ->setSortOrderDesc()
                ->setLimit(1);

            $lastFile = $this->search($searcher)->getItems()->first();
            if ($lastFile->getId() !== $dto->getId()) {
                $dto->setOrder($lastFile->getOrder() + 1);
            }
        }

        $file = $this->fileFactory->makeModelFromDto($dto, $file);
        $file = $this->fileRepository->save($file);

        return $this->fileFactory->makeDtoFromObject($file);
    }

    public function getById(int $id): ?FileDTO
    {
        $searcher = new FileSearcher();
        $searcher->setId($id);

        return $this->search($searcher)->getItems()->first();
    }

    public function deleteById(int $id): bool
    {
        $file = $this->getById($id);
        if ($file) {
            if ($this->fileRepository->deleteById($id)) {
                $this->removeFromStore($file->getPath());

                return true;
            }
        }

        return false;
    }

    public function search(FileSearcher $searcher): FileSearchResponse
    {
        $response = $this->fileRepository->search($searcher);

        $result = new FileSearchResponse();
        $result->setTotal($response->getTotal());

        $collection = new Files();
        foreach ($response->getItems() as $item) {
            $collection->add($this->fileFactory->makeDtoFromObject($item));
        }

        return $result->setItems($collection);
    }

    public function saveFileOrderIndex(FileDTO $file, int $newIndex): void
    {
        $searcher = new FileSearcher();
        $searcher->setType($file->getType())
            ->setSortOrderAsc()
            ->setSortOrderProperty(File::ORDER)
            ->setRelatedId($file->getRelatedId());

        $files = $this->search($searcher)->getItems();
        $index = 0;
        $files->map(function (FileDTO $f) use (&$index, $newIndex, $file) {
            if ($f->getId() === $file->getId()) {
                $f->setOrder($newIndex);
            }
            else {
                if ($index === $newIndex) {
                    $index++;
                }
                $f->setOrder($index);
                $index++;
            }

            return $file;
        });

        foreach ($files as $f) {
            $this->save($f);
        }
    }
}
