<?php declare(strict_types=1);

namespace Core\App\Files;

use Core\Domains\Files\FileService;
use Core\Domains\Shared\ValueObjects\UploadedFile;
use Core\Exceptions\ValidationException;

readonly class ReplaceCommand
{
    public function __construct(
        private FileService $fileService,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function execute(
        int           $replacingFileId,
        ?UploadedFile $uploadedFile,
        string        $directory,
    ): bool
    {
        $errors = [];

        $file = $this->fileService->getById($replacingFileId);

        if ( ! $file) {
            $errors['replace_file'][] = 'Заменяемый файл не существует';
        }

        if ( ! $uploadedFile) {
            $errors['upload_file'][] = 'Указанный файл не существует';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }

        $newFile = $this->fileService->store($uploadedFile, $directory);
        $this->fileService->replace($file, $newFile);

        return true;
    }
}
