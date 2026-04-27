<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\UseCases\Ticket;

use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\HelpDesk\HelpDeskServiceLocator;
use Core\Domains\HelpDesk\Services\TicketCategoryService;
use Core\Domains\HelpDesk\Services\TicketServiceService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CreateValidator
{
    private TicketCategoryService $categoryService;
    private TicketServiceService  $serviceService;

    public function __construct()
    {
        $this->categoryService = HelpDeskServiceLocator::TicketCategoryService();
        $this->serviceService  = HelpDeskServiceLocator::TicketServiceService();
    }

    /**
     * @throws ValidationException
     */
    public function validate(CreateInputDTO $input): void
    {
        $messages = [];


        $validator = Validator::make(
            (array) $input,
            [
                'typeCode'     => 'required|string|in:incident,question,proposal,service,feedback',
                'categoryCode' => 'required|string',
                'serviceCode'  => 'required|string',
                'accountId'    => 'required|integer|exists:accounts,id',
                'description'  => 'required|string|min:5',
                'contactName'  => 'nullable|string|max:255',
                'contactEmail' => 'nullable|email|max:255',
                'contactPhone' => 'nullable|string|max:20',
                'userId'       => 'nullable|integer|exists:users,id',
                'files'        => 'nullable|array|max:5',
                'files.*'      => 'file|max:20480',
            ],
        );

        if ($validator->fails()) {
            $messages = $validator->messages();
        }

        // 2. Проверка существования и активности сущностей
        $type = TicketTypeEnum::byCode($input->typeCode);
        if ( ! $type) {
            $messages['typeCode'][] = ['Неверный тип заявки'];
        }

        $category = $this->categoryService->findByTypeAndCode($type, $input->categoryCode);
        if ( ! $category || ! $category->getIsActive()) {
            $messages['categoryCode'][] = ['Категория не найдена или неактивна'];
        }

        $service = $this->serviceService->findByCategoryIdAndCode($category->getId(), $input->serviceCode);
        if ( ! $service || ! $service->getIsActive()) {
            $messages['serviceCode'][] = ['Услуга не найдена или неактивна'];
        }

        if (empty($input->contactName) && empty($input->contactEmail) && empty($input->contactPhone)) {
            $messages['contact'][] = ['Укажите хотя бы один контакт (имя, email или телефон)'];
        }

        if ($messages) {
            throw ValidationException::withMessages($messages);
        }
    }
}
