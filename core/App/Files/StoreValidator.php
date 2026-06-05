<?php declare(strict_types=1);

namespace Core\App\Files;

use Core\Domains\Shared\ValueObjects\UploadedFile;
use Core\Exceptions\ValidationException;

class StoreValidator
{
    /**
     * @param UploadedFile[] $files
     *
     * @throws ValidationException
     */
    public function validate(array $files): void
    {
        $errors = [];

        if ($files === []) {
            $errors['files'][] = 'Не выбраны файлы для загрузки';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }
    }
}
