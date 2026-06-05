<?php declare(strict_types=1);

namespace Core\App\Files;

use Core\Contracts\FileStorageInterface;
use Core\Contracts\StringServiceInterface;
use Core\Domains\Files\FileEntity;
use Core\Domains\Files\FileService;
use Core\Domains\Files\FileTypeEnum;
use Core\Domains\Shared\ValueObjects\UploadedFile;

readonly class FileTransferService
{
    public function __construct(
        private FileService            $fileService,
        private FileStorageInterface   $storage,
        private StringServiceInterface $stringGenerator,
    )
    {
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

    public function replace(FileEntity $file, FileEntity $replaceFile): FileEntity
    {
        if ($file->getExt() === $replaceFile->getExt()) {
            $this->storage->put($file->getPath(), $this->storage->get($replaceFile->getPath()));
        }
        $this->storage->delete($replaceFile->getPath());

        return $file;
    }

    public function move(FileEntity $file, string $path): FileEntity
    {
        $normalizedPath = $this->stringGenerator->normalizePath($path);
        $this->storage->put($normalizedPath, $this->storage->get($file->getPath()));
        $this->storage->delete($file->getPath());

        $file->setPath($normalizedPath);

        return $this->fileService->save($file);
    }

    public function store(
        UploadedFile $file,
        string       $directory,
        bool         $public = true,
    ): FileEntity
    {
        $baseDir = $public ? 'public/' : '';
        $dir     = $this->stringGenerator->normalizePath(sprintf('%s/%s/', $baseDir, $directory));

        $fileName = $this->generateName($dir, $file->getExtension());
        $fullPath = $this->stringGenerator->normalizePath($dir . $fileName);

        $this->storage->put($fullPath, $file->getContent(), $public);

        return new FileEntity()
            ->setName($file->getName())
            ->setExt($file->getExtension())
            ->setPath($fullPath)
        ;
    }

    /**
     * @param UploadedFile[] $files
     */
    public function storeAndSave(
        array         $files,
        ?int          $parentId = null,
        ?string       $directory = null,
        ?FileTypeEnum $type = null,
    ): void
    {
        foreach ($files as $file) {
            $this->fileService->save(
                $this->store($file, $directory ?? '', true)
                    ->setType($type)
                    ->setParentId($parentId),
            );
        }
    }

    private function generateName(string $fullDirPath, string $ext): string
    {
        do {
            $fileName = sprintf('%s.%s', $this->stringGenerator->random(8), $ext);
        } while ($this->storage->exists($this->stringGenerator->normalizePath(sprintf('%s%s', $fullDirPath, $fileName))));

        return $fileName;
    }
}
