<?php declare(strict_types=1);

namespace Core\Domains\News;

use Core\Domains\Files\FileSearcher;
use Core\Domains\Files\FileService as BaseFileService;
use Core\Domains\Files\FileTypeEnum;
use Core\Domains\Files\FileServiceConfig;
use Core\Domains\Shared\ValueObjects\UploadedFile;

readonly class FileService extends BaseFileService
{
    protected function config(): FileServiceConfig
    {
        return new FileServiceConfig(
            baseDir : 'news',
            baseType: FileTypeEnum::NEWS,
        );
    }

    /**
     * @param UploadedFile[] $files
     */
    public function storeNewsFiles(array $files, int $newsId): void
    {
        $this->storeAndSave($files, $newsId);
    }

    public function deleteNewsFiles(int $newsId): void
    {
        $files = $this->search(
            new FileSearcher()
                ->setRelatedId($newsId)
                ->setType(FileTypeEnum::NEWS),
        )->getItems();

        foreach ($files as $file) {
            $this->deleteById($file->getId());
        }
    }
}
