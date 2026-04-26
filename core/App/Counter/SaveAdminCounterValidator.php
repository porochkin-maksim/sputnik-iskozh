<?php declare(strict_types=1);

namespace Core\App\Counter;

use App\Models\Counter\Counter;
use Core\Domains\Counter\CounterSearcher;
use Core\Domains\Counter\CounterService;
use Core\Exceptions\ValidationException;
use Core\Repositories\SearcherInterface;

readonly class SaveAdminCounterValidator
{
    public function __construct(
        private CounterService $counterService,
    )
    {
    }

    public function validate(?int $id, ?string $number): void
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

            if ($exists !== null && $exists->getId() !== $id) {
                $errors['number'][] = 'Такой счётчик уже существует в системе';
            }
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }
    }
}
