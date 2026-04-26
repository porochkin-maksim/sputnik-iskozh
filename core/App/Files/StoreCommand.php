<?php declare(strict_types=1);

namespace Core\App\Files;

use Core\Domains\Files\FileService;
use Core\Domains\Files\FileTypeEnum;
use Core\Domains\Folders\FolderService;
use Core\Domains\Shared\ValueObjects\UploadedFile;
use Core\Exceptions\ValidationException;

readonly class StoreCommand
{
    public function __construct(
        private FileService    $fileService,
        private FolderService  $folderService,
        private StoreValidator $validator,
    )
    {
    }

    /**
     * @param UploadedFile[] $files
     *
     * @throws ValidationException
     */
    public function execute(
        array         $files,
        string        $directory,
        ?int          $parentId = null,
        ?FileTypeEnum $type = null,
    ): bool
    {
        $this->validator->validate($files);

        if ($parentId !== null && $type === null && ! $this->folderService->getById($parentId)) {
            throw new ValidationException(['parent_id' => ['Указанный каталог не существует']]);
        }

        $this->fileService->storeAndSave($files, $parentId, $type, $directory);

        return true;
    }
}
