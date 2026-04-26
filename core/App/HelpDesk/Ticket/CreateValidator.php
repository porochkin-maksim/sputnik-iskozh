<?php declare(strict_types=1);

namespace Core\App\HelpDesk\Ticket;

use Core\Domains\Account\AccountService;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\HelpDesk\Services\TicketCategoryService;
use Core\Domains\HelpDesk\Services\TicketCatalogService;
use Core\Domains\User\UserService;
use Core\Exceptions\ValidationException;

readonly class CreateValidator
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
    public function validate(CreateInput $input): void
    {
        $errors = [];

        if (trim($input->typeCode) === '') {
            $errors['typeCode'][] = 'Укажите тип заявки';
        }
        if (trim($input->categoryCode) === '') {
            $errors['categoryCode'][] = 'Укажите категорию';
        }
        if (trim($input->serviceCode) === '') {
            $errors['serviceCode'][] = 'Укажите услугу';
        }

        if ($input->accountId === null || $input->accountId <= 0) {
            $errors['accountId'][] = 'Укажите участок';
        }
        elseif ( ! $this->accountService->getById($input->accountId)) {
            $errors['accountId'][] = 'Участок не найден';
        }

        if (trim($input->description) === '') {
            $errors['description'][] = 'Укажите описание';
        }
        elseif (mb_strlen($input->description) < 5) {
            $errors['description'][] = 'Описание должно содержать не менее 5 символов';
        }

        if ($input->contactName !== null && mb_strlen($input->contactName) > 255) {
            $errors['contactName'][] = 'Имя не должно превышать 255 символов';
        }

        if ($input->contactEmail !== null) {
            if (mb_strlen($input->contactEmail) > 255) {
                $errors['contactEmail'][] = 'Email не должен превышать 255 символов';
            }
            elseif ( ! filter_var($input->contactEmail, FILTER_VALIDATE_EMAIL)) {
                $errors['contactEmail'][] = 'Некорректный формат email';
            }
        }

        if ($input->contactPhone !== null && mb_strlen($input->contactPhone) > 20) {
            $errors['contactPhone'][] = 'Телефон не должен превышать 20 символов';
        }

        if ($input->userId !== null && ! $this->userService->getById($input->userId, true)) {
            $errors['userId'][] = 'Пользователь не найден';
        }

        if (count($input->files) > 5) {
            $errors['files'][] = 'Нельзя прикрепить больше 5 файлов';
        }

        foreach ($input->files as $index => $file) {
            if ($file->getSize() > 20480 * 1024) {
                $errors["files.$index"][] = 'Размер файла не должен превышать 20 МБ';
            }
        }

        $type = TicketTypeEnum::byCode($input->typeCode);
        if ( ! $type) {
            $errors['typeCode'][] = 'Неверный тип заявки';
        }

        $category = $this->categoryService->findByTypeAndCode($type, $input->categoryCode);
        if ( ! $category || ! $category->getIsActive()) {
            $errors['categoryCode'][] = 'Категория не найдена или неактивна';
        }

        $service = $category
            ? $this->serviceService->findByCategoryIdAndCode($category->getId(), $input->serviceCode)
            : null;
        if ( ! $service || ! $service->getIsActive()) {
            $errors['serviceCode'][] = 'Услуга не найдена или неактивна';
        }

        if (empty($input->contactName) && empty($input->contactEmail) && empty($input->contactPhone)) {
            $errors['contact'][] = 'Укажите хотя бы один контакт (имя, email или телефон)';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }
    }
}
