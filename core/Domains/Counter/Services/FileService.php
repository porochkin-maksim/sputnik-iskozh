<?php declare(strict_types=1);

namespace Core\Domains\Counter\Services;

use Core\Domains\File\Enums\FileTypeEnum;
use Core\Domains\File\Models\FileDTO;
use Core\Domains\File\Models\FileSearcher;
use Core\Domains\File\Services\FileService as BaseFileService;
use Illuminate\Http\UploadedFile;

class FileService
{
    private const string FILE_DIR = 'counters';

    public function __construct(
        private readonly BaseFileService $fileService,
    )
    {
    }

    public function storeHistoryFile(UploadedFile $file, int $historyId): FileDTO
    {
        $dto = $this->fileService->store($file, self::FILE_DIR, false);
        $dto->setType(FileTypeEnum::COUNTER_HISTORY)
            ->setRelatedId($historyId)
        ;

        $this->fileService->save($dto);

        return $dto;
    }

    public function storePassportFile(UploadedFile $file, int $counterId): FileDTO
    {
        $dto = $this->fileService->store($file, self::FILE_DIR, false);
        $dto->setType(FileTypeEnum::COUNTER_PASSPORT)
            ->setRelatedId($counterId)
        ;

        return $this->fileService->save($dto);
    }

    public function getById(int $id): ?FileDTO
    {
        return $this->fileService->getById($id);
    }

    public function deleteById(?int $id): bool
    {
        return $this->fileService->deleteById($id);
    }

    public function save(FileDTO $file): FileDTO
    {
        return $this->fileService->save($file);
    }

    public function getByHistoryId(?int $historyId): ?FileDTO
    {
        $searcher = new FileSearcher();
        $searcher->setType(FileTypeEnum::COUNTER_HISTORY)
            ->setRelatedId($historyId)
        ;

        return $this->fileService->search($searcher)->getItems()->first();
    }
}
