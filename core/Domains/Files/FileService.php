<?php declare(strict_types=1);

namespace Core\Domains\Files;

use App\Models\Files\FileModel;
use Core\Domains\Shared\Contracts\FileStorageInterface;
use Core\Domains\Shared\Contracts\StringGeneratorInterface;
use Core\Domains\Shared\ValueObjects\UploadedFile;
use Core\Repositories\SearcherInterface;

readonly class FileService
{
    public function __construct(
        private FileRepositoryInterface  $fileRepository,
        private FileStorageInterface     $storage,
        private StringGeneratorInterface $stringGenerator,
    )
    {
    }

    protected function getBaseDir(): string
    {
        return '';
    }

    protected function getBaseType(): ?FileTypeEnum
    {
        return null;
    }

    protected function isDefaultPublic(): ?bool
    {
        return true;
    }

    public function storeAndSave(
        UploadedFile|array $file,
        ?int               $parentId = null,
        ?FileTypeEnum      $type = null,
        ?string            $directory = null,
    ): void
    {
        $files = $file instanceof UploadedFile ? [$file] : $file;

        foreach ($files as $f) {
            $this->save(
                $this->store($f, $directory ? : $this->getBaseDir(), $this->isDefaultPublic())
                    ->setType($type ? : $this->getBaseType())
                    ->setParentId($parentId),
            );
        }
    }

    public function store(
        UploadedFile $file,
        string       $directory = '',
        bool         $public = true,
    ): FileEntity
    {
        $baseDir = $public ? 'public/' : '';
        $dir     = $this->stringGenerator->normalizePath(sprintf('%s/%s/', $baseDir, $directory ? : $this->getBaseDir()));

        $fileName = $this->generateName($dir, $file->getExtension());
        $fullPath = $this->stringGenerator->normalizePath($dir . $fileName);

        $this->storage->put($fullPath, $file->getContent(), $public);

        return new FileEntity()
            ->setName($file->getName())
            ->setExt($file->getExtension())
            ->setPath($fullPath)
        ;
    }

    public function copy(FileEntity $file): FileEntity
    {
        $newFile = clone $file;

        $pureName = $file->getTrueFileName(false);
        $newName  = $this->generateName($file->getDir(), $file->getExt());
        $newPath  = $this->stringGenerator->replace(
            sprintf('%s.%s', $pureName, $file->getExt()),
            $newName,
            $newFile->getPath(),
        );

        $this->storage->copy($file->getPath(), $newPath);
        $newFile
            ->setId(null)
            ->setPath($this->stringGenerator->normalizePath($newPath))
        ;

        return $newFile;
    }

    public function generateName(string $fullDirPath, string $ext): string
    {
        do {
            $fileName = sprintf('%s.%s', $this->stringGenerator->random(8), $ext);
        } while ($this->fileExists($this->stringGenerator->normalizePath(sprintf('%s%s', $fullDirPath, $fileName))));

        return $fileName;
    }

    public function fileExists(string $fullPath): bool
    {
        return $this->storage->exists($fullPath);
    }

    public function removeFromStorage(string $path): bool
    {
        return $this->storage->delete($path);
    }

    public function search(FileSearcher $searcher): FileSearchResponse
    {
        return $this->fileRepository->search($searcher);
    }

    public function getById(?int $id): ?FileEntity
    {
        return $this->fileRepository->getById($id);
    }

    public function save(FileEntity $file): FileEntity
    {
        if ( ! $file->getId()) {
            $lastFile = $this->search(new FileSearcher()
                ->setType($file->getType())
                ->setSortOrderProperty(FileModel::ORDER, SearcherInterface::SORT_ORDER_DESC)
                ->setLimit(1),
            )->getItems()->first();
            if ($lastFile?->getId() !== $file->getId()) {
                $file->setOrder((int) $lastFile?->getOrder() + 1);
            }
        }

        return $this->fileRepository->save($file);
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

    public function saveFileOrderIndex(FileEntity $file, int $newIndex): void
    {
        $files = $this->search(new FileSearcher()
            ->setType($file->getType())
            ->setSortOrderProperty(FileModel::ORDER, SearcherInterface::SORT_ORDER_ASC)
            ->setRelatedId($file->getRelatedId()),
        )->getItems();

        $index = 0;
        $files->map(function (FileEntity $f) use (&$index, $newIndex, $file) {
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

    public function replace(FileEntity $file, FileEntity $replaceFile): FileEntity
    {
        if ($file->getExt() === $replaceFile->getExt()) {
            $this->storage->put($file->getPath(), $this->storage->get($replaceFile->getPath()));
        }
        $this->removeFromStorage($replaceFile->getPath());

        return $file;
    }

    public function move(FileEntity $file, string $path): FileEntity
    {
        $this->storage->put($this->stringGenerator->normalizePath($path), $this->storage->get($file->getPath()));
        $this->removeFromStorage($file->getPath());

        $file->setPath($path);

        return $this->save($file);
    }

    public function getByPath($filePath): ?FileEntity
    {
        $searcher = new FileSearcher()
            ->addWhere(FileModel::PATH, SearcherInterface::EQUALS, $filePath)
            ->setLimit(1)
        ;

        return $this->search($searcher)->getItems()->first();
    }

    public function deleteById(?int $id): bool
    {
        $file = $this->getById($id);
        if ($file && $this->fileRepository->deleteById($id)) {
            return $this->removeFromStorage($file->getPath());
        }

        return false;
    }

    public function deleteByTypeAndRelatedId(FileTypeEnum $type, int $relatedId): void
    {
        $files = $this->search(new FileSearcher()
            ->setRelatedId($relatedId)
            ->setType($type),
        )->getItems();

        foreach ($files as $file) {
            $this->deleteById($file->getId());
        }
    }
}
