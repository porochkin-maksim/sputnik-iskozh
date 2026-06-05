<?php declare(strict_types=1);

namespace Core\App\News;

use Core\Domains\News\NewsCategoryEnum;
use Core\Exceptions\ValidationException;

class SaveValidator
{
    /**
     * @throws ValidationException
     */
    public function validate(
        string   $title,
        ?string  $description,
        int|null $category,
    ): void
    {
        $errors = [];

        if (trim($title) === '') {
            $errors['title'][] = 'Укажите название';
        }
        elseif (mb_strlen($title) > 255) {
            $errors['title'][] = 'Название не должно превышать 255 символов';
        }

        if ($description !== null && mb_strlen($description) > 255) {
            $errors['description'][] = 'Описание не должно превышать 255 символов';
        }

        if ($category === null) {
            $errors['category'][] = 'Укажите категорию';
        }
        elseif (NewsCategoryEnum::tryFrom($category) === null) {
            $errors['category'][] = 'Указана некорректная категория';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }
    }
}
