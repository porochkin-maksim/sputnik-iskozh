<?php declare(strict_types=1);

namespace Core\App\Files;

use App\Models\Files\FileModel;
use Core\Exceptions\ValidationException;

class GetListValidator
{
    private const array ALLOWED_SORT_FIELDS = [
        FileModel::NAME,
        'created_at',
        'updated_at',
        FileModel::ORDER,
    ];

    /**
     * @throws ValidationException
     */
    public function validate(?int $limit, ?string $sortField): void
    {
        $errors = [];

        if ($limit !== null && $limit <= 0) {
            $errors['limit'][] = 'Лимит должен быть больше нуля';
        }

        if ($sortField !== null && ! in_array($sortField, self::ALLOWED_SORT_FIELDS, true)) {
            $errors['sort_by'][] = 'Указано некорректное поле сортировки';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }
    }
}
