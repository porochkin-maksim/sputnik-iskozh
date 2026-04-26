<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Services;

use Core\Domains\Files\Entities\FileDTO;
use Core\Domains\Files\Enums\FileTypeEnum;
use Core\Domains\Files\Services\FileService as BaseFileService;
use Core\Domains\Shared\ValueObjects\UploadedFile;

class FileService
{
    private const string FILE_DIR = 'help-desk/tickets';

    public function __construct(
        private readonly BaseFileService $fileService,
    )
    {
    }

    public function store(UploadedFile $file, int $relatedId, FileTypeEnum $type = FileTypeEnum::TICKET): FileDTO
    {
        $dto = $this->fileService->store($file, self::FILE_DIR, false);
        $dto->setType($type)
            ->setRelatedId($relatedId)
        ;

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
