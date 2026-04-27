<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\UseCases\Service;

use Core\Domains\HelpDesk\HelpDeskServiceLocator;
use Core\Domains\HelpDesk\Models\TicketServiceDTO;
use Core\Domains\HelpDesk\Searchers\TicketServiceSearcher;
use Core\Domains\HelpDesk\Services\TicketServiceService;
use Illuminate\Validation\ValidationException;

class SaveValidator
{
    private TicketServiceService $ticketServiceService;

    public function __construct()
    {
        $this->ticketServiceService = HelpDeskServiceLocator::TicketServiceService();
    }

    /**
     * @throws  ValidationException
     */
    public function validate(TicketServiceDTO $dto): void
    {
        $errors = [];

        if ( ! $dto->getCategoryId()) {
            $errors['category_id'][] = 'Категория обязательна';
        }

        if (empty($dto->getName())) {
            $errors['name'][] = 'Название услуги обязательно';
        }
        elseif (mb_strlen($dto->getName()) > 100) {
            $errors['name'][] = 'Название услуги не должно превышать 100 символов';
        }

        if (empty($dto->getCode())) {
            $errors['code'][] = 'Код услуги обязателен';
        }
        elseif ( ! preg_match('/^[a-z0-9_-]+$/', $dto->getCode())) {
            $errors['code'][] = 'Код может содержать только латинские буквы, цифры, тире и подчёркивание';
        }
        elseif ($dto->getCategoryId()) {
            $exists = $this->ticketServiceService->search(new TicketServiceSearcher()
                ->setCategoryId($dto->getCategoryId())
                ->setCode($dto->getCode())
                ->setLimit(1),
            )->getItems()->first();

            if ($exists && $exists->getId() !== $dto->getId()) {
                $errors['code'][] = 'Такая услуга уже существует';
            }
        }

        if ( ! empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }
}
