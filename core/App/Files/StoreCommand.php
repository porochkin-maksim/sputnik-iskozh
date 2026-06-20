<?php declare(strict_types=1);

namespace Core\App\Files;

use Core\Domains\Files\FileTypeEnum;
use Core\Domains\Folders\FolderService;
use Core\Domains\Shared\ValueObjects\UploadedFile;
use Core\Exceptions\ValidationException;

readonly class StoreCommand
{
    public function __construct(
        private FolderService       $folderService,
        private FileTransferService $fileTransferService,
        private StoreValidator      $validator,
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
        ?int          $relatedId = null,
        ?FileTypeEnum $type = null,
    ): bool
    {
        $this->validator->validate($files);

        if ($relatedId !== null && $type === null && ! $this->folderService->getById($relatedId)) {
            throw new ValidationException(['related_id' => ['Указанный каталог не существует']]);
        }

        $this->fileTransferService->storeAndSave($files, $relatedId, $directory, $type);

        return true;
    }
}
