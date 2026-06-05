<?php declare(strict_types=1);

namespace Core\App\News;

use Core\Exceptions\ValidationException;

class GetListValidator
{
    /**
     * @throws ValidationException
     */
    public function validate(
        ?int    $limit,
        ?int    $offset,
        ?string $search,
    ): void
    {
        $errors = [];

        if ($limit !== null && $limit <= 0) {
            $errors['limit'][] = 'Лимит должен быть больше нуля';
        }

        if ($offset !== null && $offset < 0) {
            $errors['offset'][] = 'Смещение не может быть отрицательным';
        }

        if ($search !== null && mb_strlen($search) > 255) {
            $errors['search'][] = 'Поисковый запрос не должен превышать 255 символов';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }
    }
}
