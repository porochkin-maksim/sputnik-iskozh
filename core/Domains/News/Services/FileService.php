<?php declare(strict_types=1);

namespace Core\Domains\News\Services;

use Core\Domains\File\Enums\FileTypeEnum;
use Core\Domains\File\Models\FileDTO;
use Core\Domains\File\Services\FileService as BaseFileService;
use Illuminate\Http\UploadedFile;

class FileService
{
    private const FILE_DIR = 'news';

    public function __construct(
        private readonly BaseFileService $fileService,
    )
    {
    }

    public function store(UploadedFile $file, int $newsId): FileDTO
    {
        $dto = $this->fileService->store($file, self::FILE_DIR);
        $dto->setType(FileTypeEnum::NEWS)
            ->setRelatedId($newsId);

        $this->fileService->save($dto);

        return $dto;
    }

    public function getById(int $id): ?FileDTO
    {
        return $this->fileService->getById($id);
    }

    public function deleteById(int $id): bool
    {
        return $this->fileService->deleteById($id);
    }

    public function save(FileDTO $file): FileDTO
    {
        return $this->fileService->save($file);
    }
}
