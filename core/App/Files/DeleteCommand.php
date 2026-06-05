<?php declare(strict_types=1);

namespace Core\App\Files;

use Core\Domains\Files\FileService;
use Core\Exceptions\ValidationException;

readonly class DeleteCommand
{
    public function __construct(
        private FileService $fileService,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function execute(int $id): bool
    {
        if ( ! $this->fileService->getById($id)) {
            throw new ValidationException(['id' => ['Указанный файл не существует']]);
        }

        return $this->fileService->deleteById($id);
    }
}
