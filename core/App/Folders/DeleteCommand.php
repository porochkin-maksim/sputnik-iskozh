<?php declare(strict_types=1);

namespace Core\App\Folders;

use Core\App\Folders\Delete\FolderDeletionService;
use Core\Domains\Folders\FolderService;
use Core\Exceptions\ValidationException;

readonly class DeleteCommand
{
    public function __construct(
        private FolderService         $folderService,
        private FolderDeletionService $folderDeletionService,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function execute(int $id): bool
    {
        if ( ! $this->folderService->getById($id)) {
            throw new ValidationException(['id' => ['Указанная папка не существует']]);
        }

        return $this->folderDeletionService->deleteById($id);
    }
}
