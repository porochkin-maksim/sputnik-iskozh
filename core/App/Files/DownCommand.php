<?php declare(strict_types=1);

namespace Core\App\Files;

use Core\Domains\Files\FileService;
use Core\Exceptions\ValidationException;

readonly class DownCommand
{
    public function __construct(
        private FileService $fileService,
        private FileOrderingService $fileOrderingService,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function execute(int $fileId, int $oldIndex): bool
    {
        $file = $this->fileService->getById($fileId);

        if ( ! $file) {
            throw new ValidationException(['file' => ['Указанный файл не существует']]);
        }

        $this->fileOrderingService->saveFileOrderIndex($file, $oldIndex + 1);

        return true;
    }
}
