<?php declare(strict_types=1);

namespace Core\Domains\Files;

use Core\Contracts\FileStorageInterface;
use App\Models\Files\FileModel;
use Core\Contracts\StringServiceInterface;
use Core\Repositories\SearcherInterface;
use Core\Domains\Shared\ValueObjects\UploadedFile;

readonly class FileService
{
    public function __construct(
        private FileRepositoryInterface $fileRepository,
        private FileStorageInterface    $storage,
        private StringServiceInterface  $stringGenerator,
    )
    {
    }

    protected function config(): FileServiceConfig
    {
        return new FileServiceConfig();
    }

    public function search(FileSearcher $searcher): FileSearchResponse
    {
        return $this->fileRepository->search($searcher);
    }

    /**
     * @param UploadedFile[] $file
     */
    public function storeAndSave(
        array|UploadedFile $file,
        ?int $parentId = null,
        ?FileTypeEnum $type = null,
        ?string $directory = null,
    ): void
    {
        $files = $file instanceof UploadedFile ? [$file] : $file;
        $config = $this->config();

        foreach ($files as $currentFile) {
            $this->save(
                $this->store(
                    $currentFile,
                    $directory ?? $config->baseDir,
                    $config->defaultPublic,
                )
                    ->setType($type ?? $config->baseType)
                    ->setParentId($parentId),
            );
        }
    }

    protected function store(
        UploadedFile $file,
        string       $directory = '',
        bool         $public = true,
    ): FileEntity
    {
        $config  = $this->config();
        $baseDir = $public ? 'public/' : '';
        $dir     = $this->normalizePath(sprintf('%s/%s/', $baseDir, $directory ? : $config->baseDir));

        $fileName = $this->generateName($dir, $file->getExtension());
        $fullPath = $this->normalizePath($dir . $fileName);

        $this->storage->put($fullPath, $file->getContent(), $public);

        return new FileEntity()
            ->setName($file->getName())
            ->setExt($file->getExtension())
            ->setPath($fullPath)
        ;
    }

    public function removeFromStorage(string $path): bool
    {
        return $this->storage->delete($path);
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

    protected function generateName(string $fullDirPath, string $ext): string
    {
        do {
            $fileName = sprintf('%s.%s', $this->stringGenerator->random(8), $ext);
        } while ($this->storage->exists($this->normalizePath(sprintf('%s%s', $fullDirPath, $fileName))));

        return $fileName;
    }

    protected function normalizePath(string $path): string
    {
        return $this->stringGenerator->normalizePath($path);
    }
}
