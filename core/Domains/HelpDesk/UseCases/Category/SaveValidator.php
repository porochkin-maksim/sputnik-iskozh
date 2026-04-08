<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\UseCases\Category;

use Core\Domains\HelpDesk\HelpDeskServiceLocator;
use Core\Domains\HelpDesk\Models\TicketCategoryDTO;
use Core\Domains\HelpDesk\Searchers\TicketCategorySearcher;
use Core\Domains\HelpDesk\Services\TicketCategoryService;
use Illuminate\Validation\ValidationException;

class SaveValidator
{
    private TicketCategoryService $ticketCategoryService;

    public function __construct()
    {
        $this->ticketCategoryService = HelpDeskServiceLocator::TicketCategoryService();
    }

    /**
     * @throws  ValidationException
     */
    public function validate(TicketCategoryDTO $dto): void
    {
        $errors = [];

        if ( ! $dto->getType()) {
            $errors['type'][] = 'Тип категории обязателен';
        }

        if (empty($dto->getName())) {
            $errors['name'][] = 'Название категории обязательно';
        }
        elseif (mb_strlen($dto->getName()) > 100) {
            $errors['name'][] = 'Название категории не должно превышать 100 символов';
        }

        if (empty($dto->getCode())) {
            $errors['code'][] = 'Код категории обязателен';
        }
        elseif ( ! preg_match('/^[a-z0-9_-]+$/', $dto->getCode())) {
            $errors['code'][] = 'Код может содержать только латинские буквы, цифры, тире и подчёркивание';
        }
        elseif ($dto->getType()) {
            $exists = $this->ticketCategoryService->search(new TicketCategorySearcher()
                ->setType($dto->getType())
                ->setCode($dto->getCode())
                ->setLimit(1)
            )->getItems()->first();

            if ($exists && $exists->getId() !== $dto->getId()) {
                $errors['code'][] = 'Такая категория уже существует';
            }
        }

        if ( ! empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }
}
