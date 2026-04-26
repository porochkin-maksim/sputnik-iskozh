<?php declare(strict_types=1);

namespace Core\App\Options;

use Core\Domains\Option\Enums\OptionEnum;
use Core\Domains\Option\OptionService;
use Core\Exceptions\ValidationException;

readonly class SaveValidator
{
    public function __construct(
        private OptionService $optionService,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function validate(int $id, array $data): void
    {
        $errors = [];

        if ( ! OptionEnum::tryFrom($id)) {
            $errors['id'][] = 'Опция не найдена';
        } elseif ( ! $this->optionService->getById($id)) {
            $errors['id'][] = 'Опция не найдена';
        }

        if ( ! is_array($data)) {
            $errors['data'][] = 'Данные должны быть массивом';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }
    }
}
