<?php declare(strict_types=1);

namespace Core\App\HelpDesk\Service;

use Core\Domains\HelpDesk\Models\TicketServiceEntity;
use Core\Domains\HelpDesk\Searchers\TicketServiceSearcher;
use Core\Domains\HelpDesk\Services\TicketCatalogService;
use Core\Exceptions\ValidationException;

readonly class SaveValidator
{
    public function __construct(
        private TicketCatalogService $ticketServiceService,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function validate(TicketServiceEntity $service): void
    {
        $errors = [];

        if ( ! $service->getCategoryId()) {
            $errors['category_id'][] = 'Категория обязательна';
        }

        if (empty($service->getName())) {
            $errors['name'][] = 'Название услуги обязательно';
        }
        elseif (mb_strlen($service->getName()) > 100) {
            $errors['name'][] = 'Название услуги не должно превышать 100 символов';
        }

        if (empty($service->getCode())) {
            $errors['code'][] = 'Код услуги обязателен';
        }
        elseif ( ! preg_match('/^[a-z0-9_-]+$/', $service->getCode())) {
            $errors['code'][] = 'Код может содержать только латинские буквы, цифры, тире и подчёркивание';
        }
        elseif ($service->getCategoryId()) {
            $searcher = new TicketServiceSearcher();
            $searcher
                ->setCategoryId($service->getCategoryId())
                ->setCode($service->getCode())
                ->setLimit(1)
            ;

            $exists = $this->ticketServiceService->search($searcher)->getItems()->first();
            if ($exists && $exists->getId() !== $service->getId()) {
                $errors['code'][] = 'Такая услуга уже существует';
            }
        }

        if ($errors) {
            throw new ValidationException($errors);
        }
    }
}
