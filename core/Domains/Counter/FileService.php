<?php declare(strict_types=1);

namespace Core\Domains\Counter;

use Core\Domains\Files\FileEntity;
use Core\Domains\Files\FileSearcher;
use Core\Domains\Files\FileService as BaseFileService;
use Core\Domains\Files\FileTypeEnum;
use Core\Domains\Shared\ValueObjects\UploadedFile;

readonly class FileService extends BaseFileService
{
    protected function getBaseDir(): string
    {
        return 'counters';
    }

    public function storeHistoryFile(UploadedFile $file, int $historyId): FileEntity
    {
        $entity = $this->store($file, $this->getBaseDir(), false)
            ->setType(FileTypeEnum::COUNTER_HISTORY)
            ->setRelatedId($historyId)
        ;

        $this->save($entity);

        return $entity;
    }

    public function storePassportFile(UploadedFile $file, int $counterId): FileEntity
    {
        $entity = $this->store($file, $this->getBaseDir(), false)
            ->setType(FileTypeEnum::COUNTER_PASSPORT)
            ->setRelatedId($counterId)
        ;

        return $this->save($entity);
    }

    public function getByHistoryId(?int $historyId): ?FileEntity
    {
        $searcher = new FileSearcher()
            ->setType(FileTypeEnum::COUNTER_HISTORY)
            ->setRelatedId($historyId)
        ;

        return $this->search($searcher)->getItems()->first();
    }
}
