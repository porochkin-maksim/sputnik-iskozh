<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\UseCases\Ticket;

use Core\Domains\Account\AccountLocator;
use Core\Domains\Account\Services\AccountService;
use Core\Domains\HelpDesk\Enums\TicketPriorityEnum;
use Core\Domains\HelpDesk\Enums\TicketStatusEnum;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\HelpDesk\HelpDeskServiceLocator;
use Core\Domains\HelpDesk\Services\TicketCategoryService;
use Core\Domains\HelpDesk\Services\TicketServiceService;
use Core\Domains\User\Services\UserService;
use Core\Domains\User\UserLocator;
use Core\Exceptions\ValidationException;

class UpdateValidator
{
    private TicketCategoryService $categoryService;
    private TicketServiceService  $serviceService;
    private AccountService        $accountService;
    private UserService           $userService;

    public function __construct()
    {
        $this->categoryService = HelpDeskServiceLocator::TicketCategoryService();
        $this->serviceService  = HelpDeskServiceLocator::TicketServiceService();
        $this->accountService  = AccountLocator::AccountService();
        $this->userService     = UserLocator::UserService();
    }

    /**
     * @throws ValidationException
     */
    public function validate(UpdateInputDTO $dto): void
    {
        $errors = [];

        // 1. description
        if (empty($dto->description)) {
            $errors['description'][] = 'Описание обязательно для заполнения';
        }
        elseif (mb_strlen($dto->description) < 5) {
            $errors['description'][] = 'Описание должно содержать не менее 5 символов';
        }

        // 2. type
        $allowedTypes = array_column(TicketTypeEnum::cases(), 'value');
        if ( ! in_array($dto->type, $allowedTypes, true)) {
            $errors['type'][] = 'Некорректный тип заявки';
        }

        // 3. priority
        $allowedPriorities = array_column(TicketPriorityEnum::cases(), 'value');
        if ( ! in_array($dto->priority, $allowedPriorities, true)) {
            $errors['priority'][] = 'Некорректный приоритет';
        }

        // 4. status
        $allowedStatuses = array_column(TicketStatusEnum::cases(), 'value');
        if ( ! in_array($dto->status, $allowedStatuses, true)) {
            $errors['status'][] = 'Некорректный статус';
        }
        elseif ( ! $dto->result) {
            /** @var TicketStatusEnum $status */
            $status = TicketStatusEnum::tryFrom($dto->status);
            if ($status->isClosed()) {
                $errors['result'][] = 'Укажите результат';
            }
            if ($status->isRejected()) {
                $errors['result'][] = 'Укажите причину отклонения';
            }
        }

        // 5. category_id
        if ($dto->categoryId !== null) {
            if ( ! is_int($dto->categoryId) || $dto->categoryId <= 0) {
                $errors['category_id'][] = 'Укажите категорию';
            }
            else {
                $category = $this->categoryService->getById($dto->categoryId);
                if ( ! $category) {
                    $errors['category_id'][] = 'Категория не найдена';
                }
                elseif ( ! $category->getIsActive()) {
                    $errors['category_id'][] = 'Категория неактивна';
                }
            }
        }

        // 6. service_id
        if ($dto->serviceId !== null) {
            if ( ! is_int($dto->serviceId) || $dto->serviceId <= 0) {
                $errors['service_id'][] = 'Укажите услугу';
            }
            else {
                $service = $this->serviceService->getById($dto->serviceId);
                if ( ! $service) {
                    $errors['service_id'][] = 'Услуга не найдена';
                }
                elseif ( ! $service->getIsActive()) {
                    $errors['service_id'][] = 'Услуга неактивна';
                }
                if ($service && $dto->categoryId && $service->getCategoryId() !== $dto->categoryId) {
                    $errors['service_id'][] = 'Услуга не принадлежит выбранной категории';
                }
            }
        }

        // 7. contact_name
        if ($dto->contactName === null) {
            $errors['contact_name'][] = 'Укажите контактное имя';
        } elseif (mb_strlen($dto->contactName) > 255) {
            $errors['contact_name'][] = 'Имя не может быть длиннее 255 символов';
        }

        // 8. contact_email
        if ($dto->contactEmail !== null) {
            if (mb_strlen($dto->contactEmail) > 255) {
                $errors['contact_email'][] = 'Email не может быть длиннее 255 символов';
            }
            elseif ( ! filter_var($dto->contactEmail, FILTER_VALIDATE_EMAIL)) {
                $errors['contact_email'][] = 'Некорректный формат email';
            }
        }

        // 9. contact_phone
        if ($dto->contactPhone !== null && mb_strlen($dto->contactPhone) > 20) {
            $errors['contact_phone'][] = 'Телефон не может быть длиннее 20 символов';
        }

        // 10. user_id
        if ($dto->userId !== null) {
            if ( ! is_int($dto->userId) || $dto->userId <= 0) {
                $errors['user_id'][] = 'Укажите пользователя';
            }
            elseif ( ! $this->userService->getById($dto->userId, true)) {
                $errors['user_id'][] = 'Пользователь не найден';
            }
        }

        // 11. account_id
        if ($dto->accountId !== null) {
            if ( ! is_int($dto->accountId) || $dto->accountId <= 0) {
                $errors['account_id'][] = 'Укажите участок';
            }
            else {
                if ( ! $this->accountService->getById($dto->accountId, true)) {
                    $errors['account_id'][] = 'Участок не найден';
                }
            }
        }

        // 12. Хотя бы один контакт
        if (empty($dto->contactEmail) && empty($dto->contactPhone)) {
            $errors['contact_email'][] = 'Укажите хотя бы один контакт';
            $errors['contact_phone'][] = 'Укажите хотя бы один контакт';
        }

        if ( ! empty($errors)) {
            throw new ValidationException($errors);
        }
    }
}
