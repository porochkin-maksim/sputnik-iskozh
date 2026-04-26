<?php declare(strict_types=1);

namespace Core\App\Counter;

use App\Models\Counter\Counter;
use Core\Domains\Counter\CounterSearcher;
use Core\Domains\Counter\CounterService;
use Core\Exceptions\ValidationException;
use Core\Repositories\SearcherInterface;

readonly class CreateProfileCounterValidator
{
    public function __construct(
        private CounterService $counterService,
    )
    {
    }

    public function validate(?string $number, mixed $value, mixed $historyFile): void
    {
        $errors = [];

        if ($number === null || trim($number) === '') {
            $errors['number'][] = 'Укажите номер счётчика';
        } else {
            $exists = $this->counterService->search(
                (new CounterSearcher())
                    ->addWhere(Counter::NUMBER, SearcherInterface::EQUALS, $number)
                    ->setLimit(1)
            )->getItems()->first();

            if ($exists !== null) {
                $errors['number'][] = 'Такой счётчик уже существует в системе';
            }
        }

        if ($value === null || $value === '') {
            $errors['value'][] = 'Укажите начальное показание';
        }

        if ($historyFile === null) {
            $errors['file'][] = 'Не передана фотография счётчика';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }
    }
}
