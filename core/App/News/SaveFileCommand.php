<?php declare(strict_types=1);

namespace Core\App\News;

use Core\Domains\Files\FileTypeEnum;
use Core\Domains\News\FileService;
use Core\Exceptions\ValidationException;

readonly class SaveFileCommand
{
    public function __construct(
        private FileService       $fileService,
        private SaveFileValidator $validator,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function execute(int $id, ?string $name): bool
    {
        $this->validator->validate($name);

        $file = $this->fileService->getById($id);
        if ($file === null || $file->getType() !== FileTypeEnum::NEWS) {
            return false;
        }

        $file->setName($name);
        $this->fileService->save($file);

        return true;
    }
}
