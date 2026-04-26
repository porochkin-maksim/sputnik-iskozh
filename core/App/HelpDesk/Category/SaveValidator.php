<?php declare(strict_types=1);

namespace Core\App\HelpDesk\Category;

use Core\Domains\HelpDesk\Models\TicketCategoryEntity;
use Core\Domains\HelpDesk\Searchers\TicketCategorySearcher;
use Core\Domains\HelpDesk\Services\TicketCategoryService;
use Core\Exceptions\ValidationException;

readonly class SaveValidator
{
    public function __construct(
        private TicketCategoryService $ticketCategoryService,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function validate(TicketCategoryEntity $category): void
    {
        $errors = [];

        if (! $category->getType()) {
            $errors['type'][] = 'Тип категории обязателен';
        }

        if (empty($category->getName())) {
            $errors['name'][] = 'Название категории обязательно';
        } elseif (mb_strlen($category->getName()) > 100) {
            $errors['name'][] = 'Название категории не должно превышать 100 символов';
        }

        if (empty($category->getCode())) {
            $errors['code'][] = 'Код категории обязателен';
        } elseif (! preg_match('/^[a-z0-9_-]+$/', $category->getCode())) {
            $errors['code'][] = 'Код может содержать только латинские буквы, цифры, тире и подчёркивание';
        } elseif ($category->getType()) {
            $searcher = new TicketCategorySearcher();
            $searcher
                ->setType($category->getType())
                ->setCode($category->getCode())
                ->setLimit(1);

            $exists = $this->ticketCategoryService->search($searcher)->getItems()->first();
            if ($exists && $exists->getId() !== $category->getId()) {
                $errors['code'][] = 'Такая категория уже существует';
            }
        }

        if ($errors) {
            throw new ValidationException($errors);
        }
    }
}
