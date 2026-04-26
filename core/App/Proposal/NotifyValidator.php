<?php declare(strict_types=1);

namespace Core\App\Proposal;

use Core\Domains\Shared\ValueObjects\UploadedFile;
use Core\Exceptions\ValidationException;

readonly class NotifyValidator
{
    /**
     * @param UploadedFile[] $files
     *
     * @throws ValidationException
     */
    public function validate(string $fullText, array $files): void
    {
        $errors = [];

        if (trim($fullText) === '') {
            $errors['text'][] = 'Текст предложения обязателен';
        }

        foreach ($files as $index => $file) {
            if ( ! $file instanceof UploadedFile) {
                $errors["files.$index"][] = 'Некорректный тип файла';
            }
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }
    }
}
