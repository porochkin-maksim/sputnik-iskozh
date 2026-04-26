<?php declare(strict_types=1);

namespace Core\App\HelpDesk\Ticket;

use Core\Domains\Account\AccountService;
use Core\Domains\HelpDesk\Enums\TicketPriorityEnum;
use Core\Domains\HelpDesk\Enums\TicketStatusEnum;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\HelpDesk\Services\TicketCategoryService;
use Core\Domains\HelpDesk\Services\TicketCatalogService;
use Core\Domains\User\UserService;
use Core\Exceptions\ValidationException;

readonly class UpdateValidator
{
    public function __construct(
        private TicketCategoryService $categoryService,
        private TicketCatalogService  $serviceService,
        private AccountService        $accountService,
        private UserService           $userService,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function validate(UpdateInput $input): void
    {
        $errors = [];

        if (empty($input->description)) {
            $errors['description'][] = 'Описание обязательно для заполнения';
        }
        elseif (mb_strlen($input->description) < 5) {
            $errors['description'][] = 'Описание должно содержать не менее 5 символов';
        }

        $allowedTypes = array_column(TicketTypeEnum::cases(), 'value');
        if ( ! in_array($input->type, $allowedTypes, true)) {
            $errors['type'][] = 'Некорректный тип заявки';
        }

        $allowedPriorities = array_column(TicketPriorityEnum::cases(), 'value');
        if ( ! in_array($input->priority, $allowedPriorities, true)) {
            $errors['priority'][] = 'Некорректный приоритет';
        }

        $allowedStatuses = array_column(TicketStatusEnum::cases(), 'value');
        if ( ! in_array($input->status, $allowedStatuses, true)) {
            $errors['status'][] = 'Некорректный статус';
        }
        elseif ( ! $input->result) {
            $status = TicketStatusEnum::tryFrom($input->status);
            if ($status?->isClosed()) {
                $errors['result'][] = 'Укажите результат';
            }
            if ($status?->isRejected()) {
                $errors['result'][] = 'Укажите причину отклонения';
            }
        }

        if ($input->categoryId !== null) {
            if ( ! is_int($input->categoryId) || $input->categoryId <= 0) {
                $errors['category_id'][] = 'Укажите категорию';
            }
            else {
                $category = $this->categoryService->getById($input->categoryId);
                if ( ! $category) {
                    $errors['category_id'][] = 'Категория не найдена';
                }
                elseif ( ! $category->getIsActive()) {
                    $errors['category_id'][] = 'Категория неактивна';
                }
            }
        }

        if ($input->serviceId !== null) {
            if ( ! is_int($input->serviceId) || $input->serviceId <= 0) {
                $errors['service_id'][] = 'Укажите услугу';
            }
            else {
                $service = $this->serviceService->getById($input->serviceId);
                if ( ! $service) {
                    $errors['service_id'][] = 'Услуга не найдена';
                }
                elseif ( ! $service->getIsActive()) {
                    $errors['service_id'][] = 'Услуга неактивна';
                }
                if ($service && $input->categoryId && $service->getCategoryId() !== $input->categoryId) {
                    $errors['service_id'][] = 'Услуга не принадлежит выбранной категории';
                }
            }
        }

        if ($input->contactName === null) {
            $errors['contact_name'][] = 'Укажите контактное имя';
        }
        elseif (mb_strlen($input->contactName) > 255) {
            $errors['contact_name'][] = 'Имя не может быть длиннее 255 символов';
        }

        if ($input->contactEmail !== null) {
            if (mb_strlen($input->contactEmail) > 255) {
                $errors['contact_email'][] = 'Email не может быть длиннее 255 символов';
            }
            elseif ( ! filter_var($input->contactEmail, FILTER_VALIDATE_EMAIL)) {
                $errors['contact_email'][] = 'Некорректный формат email';
            }
        }

        if ($input->contactPhone !== null && mb_strlen($input->contactPhone) > 20) {
            $errors['contact_phone'][] = 'Телефон не может быть длиннее 20 символов';
        }

        if ($input->userId !== null) {
            if ( ! is_int($input->userId) || $input->userId <= 0) {
                $errors['user_id'][] = 'Укажите пользователя';
            }
            elseif ( ! $this->userService->getById($input->userId, true)) {
                $errors['user_id'][] = 'Пользователь не найден';
            }
        }

        if ($input->accountId !== null) {
            if ( ! is_int($input->accountId) || $input->accountId <= 0) {
                $errors['account_id'][] = 'Укажите участок';
            }
            elseif ( ! $this->accountService->getById($input->accountId, true)) {
                $errors['account_id'][] = 'Участок не найден';
            }
        }

        if (empty($input->contactEmail) && empty($input->contactPhone)) {
            $errors['contact_email'][] = 'Укажите хотя бы один контакт';
            $errors['contact_phone'][] = 'Укажите хотя бы один контакт';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }
    }
}
