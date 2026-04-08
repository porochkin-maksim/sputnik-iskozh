<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\UseCases\Ticket;

use AllowDynamicProperties;
use Carbon\Carbon;
use Core\Domains\File\Enums\FileTypeEnum;
use Core\Domains\HelpDesk\Enums\TicketPriorityEnum;
use Core\Domains\HelpDesk\Enums\TicketStatusEnum;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\HelpDesk\HelpDeskServiceLocator;
use Core\Domains\HelpDesk\Models\TicketDTO;
use Core\Exceptions\ValidationException;

#[AllowDynamicProperties] class UpdateUseCase
{
    public function __construct()
    {
        $this->ticketService   = HelpDeskServiceLocator::TicketService();
        $this->categoryService = HelpDeskServiceLocator::TicketCategoryService();
        $this->serviceService  = HelpDeskServiceLocator::TicketServiceService();
        $this->fileService     = HelpDeskServiceLocator::FileService();
        $this->validator       = new UpdateValidator();
    }

    /**
     * @throws ValidationException
     */
    public function execute(UpdateInputDTO $input): TicketDTO
    {
        $this->validator->validate($input);

        $category = $this->categoryService->getById($input->categoryId);
        $service  = $this->serviceService->getById($input->serviceId);

        $ticketDto = new TicketDTO()
            ->setId($input->id)
            ->setType(TicketTypeEnum::tryFrom($input->type))
            ->setCategoryId($category?->getId())
            ->setServiceId($service?->getId())
            ->setStatus(TicketStatusEnum::tryFrom($input->status))
            ->setPriority(TicketPriorityEnum::tryFrom($input->priority))
            ->setDescription($input->description)
            ->setResult($input->result)
            ->setContactName($input->contactName)
            ->setContactEmail($input->contactEmail)
            ->setContactPhone($input->contactPhone)
            ->setAccountId($input->accountId)
            ->setUserId($input->userId)
        ;

        $status = $ticketDto->getStatus();

        if (
            $ticketDto->getResult() ||
            $status?->isClosed() ||
            $status?->isRejected()
        ) {
            $ticketDto->setResolvedAt(Carbon::now());
        }
        else {
            $ticketDto->setResolvedAt(null);
        }

        $ticket = $this->ticketService->save($ticketDto);

        foreach ($input->files as $file) {
            $this->fileService->store($file, $ticket->getId());
        }

        foreach ($input->resultFiles as $file) {
            $this->fileService->store($file, $ticket->getId(), FileTypeEnum::TICKET_RESULT);
        }

        return $ticket;
    }
}
