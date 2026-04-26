<?php declare(strict_types=1);

namespace Core\App\Files;

use Core\Domains\Files\FileService;
use Core\Domains\Folders\FolderService;
use Core\Exceptions\ValidationException;

readonly class MoveCommand
{
    public function __construct(
        private FileService   $fileService,
        private FolderService $folderService,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function execute(
        int  $fileId,
        int  $folderId,
        bool $isCopyType,
    ): bool
    {
        $errors = [];

        $file   = $this->fileService->getById($fileId);
        $folder = $this->folderService->getById($folderId);

        if ( ! $file) {
            $errors['file'][] = 'Указанный файл не существует';
        }
        if ( ! $folder) {
            $errors['folder'][] = 'Указанный каталог не существует';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }

        if ($isCopyType) {
            $file = $this->fileService->copy($file);
        }

        $file->setParentId($folderId);
        $this->fileService->save($file);

        return true;
    }
}
