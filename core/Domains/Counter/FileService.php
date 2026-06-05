<?php declare(strict_types=1);

namespace Core\Domains\Counter;

use Core\Domains\Files\FileEntity;
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
            baseDir      : 'counters',
            defaultPublic: false,
        );
    }

    public function storeHistoryFile(UploadedFile $file, int $historyId): FileEntity
    {
        return $this->storeRelatedFile($file, FileTypeEnum::COUNTER_HISTORY, $historyId);
    }

    public function storePassportFile(UploadedFile $file, int $counterId): FileEntity
    {
        return $this->storeRelatedFile($file, FileTypeEnum::COUNTER_PASSPORT, $counterId);
    }

    public function getByHistoryId(?int $historyId): ?FileEntity
    {
        return $this->getRelatedFile(FileTypeEnum::COUNTER_HISTORY, $historyId);
    }

    public function deleteHistoryById(?int $historyId): void
    {
        $this->deleteRelatedFile(FileTypeEnum::COUNTER_HISTORY, $historyId);
    }

    public function deleteRelatedFileForCounterPassport(?int $counterId): void
    {
        $this->deleteRelatedFile(FileTypeEnum::COUNTER_PASSPORT, $counterId);
    }

    public function deleteRelatedFile(FileTypeEnum $type, ?int $relatedId): void
    {
        $file = $this->getRelatedFile($type, $relatedId);

        $this->deleteById($file?->getId());
    }

    private function storeRelatedFile(
        UploadedFile $file,
        FileTypeEnum $type,
        int          $relatedId,
    ): FileEntity
    {
        $entity = $this->store($file, $this->config()->baseDir, false)
            ->setType($type)
            ->setRelatedId($relatedId)
        ;

        return $this->save($entity);
    }

    public function getRelatedFile(FileTypeEnum $type, ?int $relatedId): ?FileEntity
    {
        $searcher = new FileSearcher()
            ->setType($type)
            ->setRelatedId($relatedId)
        ;

        return $this->search($searcher)->getItems()->first();
    }
}
