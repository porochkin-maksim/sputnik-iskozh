<?php declare(strict_types=1);

namespace Core\Domains\Report\Services;

use Core\Domains\File\Enums\FileTypeEnum;
use Core\Domains\File\Models\FileDTO;
use Core\Domains\File\Services\FileService as BaseFileService;
use Illuminate\Http\UploadedFile;

class FileService
{
    private const FILE_DIR = 'reports';

    public function __construct(
        private readonly BaseFileService $fileService,
    )
    {
    }

    public function save(UploadedFile $file, int $reportId): FileDTO
    {
        $dto = $this->fileService->store($file, self::FILE_DIR);
        $dto
            ->setType(FileTypeEnum::REPORT)
            ->setRelatedId($reportId);

        $this->fileService->save($dto);

        return $dto;
    }

    public function getById(int $id): ?FileDTO
    {
        return $this->fileService->getById($id);
    }
}
