<?php declare(strict_types=1);

namespace Core\Domains\News\Services;

use Core\Domains\File\Enums\TypeEnum;
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

    public function save(UploadedFile $file, int $newsId): FileDTO
    {
        $dto = $this->fileService->store($file, self::FILE_DIR);
        $dto
            ->setType(TypeEnum::NEWS)
            ->setRelatedId($newsId);

        $this->fileService->save($dto);

        return $dto;
    }

    public function getById(int $id): ?FileDTO
    {
        return $this->fileService->getById($id);
    }
}
