<?php declare(strict_types=1);

namespace Core\Domains\File\Services;

use App\Models\File\File;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\File\Collections\Files;
use Core\Domains\File\Factories\FileFactory;
use Core\Domains\File\Models\FileDTO;
use Core\Domains\File\Models\FileSearcher;
use Core\Domains\File\Repositories\FileRepository;
use Core\Domains\File\Responses\FileSearchResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        $dir     = $this->normalizePath(sprintf('%s/%s/', $baseDir, $directory));
        $path    = $file->storePubliclyAs(
            $dir,
            $this->generateName(
                storage_path($dir),
                $file->getClientOriginalExtension()
            )
        );

        $dto = new FileDto();
        $dto
            ->setName($file->getClientOriginalName())
            ->setExt($file->getClientOriginalExtension())
            ->setPath($this->normalizePath($path));

        return $dto;
    }

    public function copy(?FileDTO $file): FileDTO
    {
        $newFile = clone $file;

        $pureName = $file->getTrueFileName(false);
        $newName  = $this->generateName($file->getDir(), $file->getExt());
        $newPath  = Str::replace(sprintf('%s.%s', $pureName, $file->getExt()), $newName, $newFile->getPath());

        Storage::copy($file->getPath(), $newPath);
        $newFile->setId(null)
            ->setPath($this->normalizePath($newPath));

        return $newFile;
    }

    public function normalizePath(string $path): string
    {
        return Str::replace('//', '/', $path);
    }

    public function generateName(string $fullDirPath, string $ext): string
    {
        do {
            $fileName = sprintf('%s.%s', Str::random(8), $ext);
        } while ($this->fileExists($this->normalizePath(sprintf('%s%s', $fullDirPath, $fileName))));

        return $fileName;
    }

    public function fileExists(string $fullPath): bool
    {
        return \Illuminate\Support\Facades\File::exists($fullPath);
    }

    public function removeFromStorage(string $path): bool
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
                ->setSortOrderProperty(File::ORDER, SearcherInterface::SORT_ORDER_DESC)
                ->setLimit(1);

            $lastFile = $this->search($searcher)->getItems()->first();
            if ($lastFile?->getId() !== $dto->getId()) {
                $dto->setOrder((int) $lastFile?->getOrder() + 1);
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

    public function deleteById(?int $id): bool
    {
        $file = $id ? $this->getById($id) : null;
        if ($file && $this->fileRepository->deleteById($id)) {
            $this->removeFromStorage($file->getPath());

            return true;
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

    /**
     * @return int[]
     */
    public function getIdsByFullTextSearch(FileSearcher $searcher): array
    {
        if ($searcher->getSearch()) {
            return $this->fileRepository->getIdsByFullTextSearch($searcher->getSearch());
        }

        return [];
    }

    public function saveFileOrderIndex(FileDTO $file, int $newIndex): void
    {
        $searcher = new FileSearcher();
        $searcher->setType($file->getType())
            ->setSortOrderProperty(File::ORDER, SearcherInterface::SORT_ORDER_ASC)
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


        $models = collect($this->fileRepository->getByIds($files->getIds()));
        foreach ($files as $file) {
            $model = $models->where('id', $file->getId())->first();
            $model = $this->fileFactory->makeModelFromDto($file, $model);
            $this->fileRepository->save($model);
        }
    }

    public function replace(FileDTO $file, FileDTO $replaceFile): FileDTO
    {
        if ($file->getExt() === $replaceFile->getExt()) {
            Storage::put($file->getPath(), Storage::get($replaceFile->getPath()));
        }
        $this->removeFromStorage($replaceFile->getPath());

        return $file;
    }

    public function move(FileDTO $file, string $path): FileDTO
    {
        Storage::put($this->normalizePath($path), Storage::get($file->getPath()));
        $this->removeFromStorage($file->getPath());

        $file->setPath($path);

        return $this->save($file);
    }

    public function getByPath($filePath): ?FileDTO
    {
        $searcher = new FileSearcher();
        $searcher->addWhere(File::PATH, SearcherInterface::EQUALS, $filePath)
            ->setLimit(1);

        return $this->search($searcher)->getItems()->first();
    }
}
