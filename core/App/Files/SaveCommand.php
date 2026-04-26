<?php declare(strict_types=1);

namespace Core\App\Files;

use Core\Domains\Files\FileService;
use Core\Exceptions\ValidationException;

readonly class SaveCommand
{
    public function __construct(
        private FileService $fileService,
        private SaveValidator $validator,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function execute(int $id, string $name): bool
    {
        $this->validator->validate($name);

        $file = $this->fileService->getById($id);
        if ( ! $file) {
            throw new ValidationException(['id' => ['Указанный файл не существует']]);
        }

        $file->setName(trim($name));
        $this->fileService->save($file);

        return true;
    }
}
