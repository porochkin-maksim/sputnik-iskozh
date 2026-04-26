<?php declare(strict_types=1);

namespace Core\App\HelpDesk\Ticket;

use Carbon\Carbon;
use Core\Domains\Files\FileTypeEnum;
use Core\Domains\HelpDesk\Enums\TicketPriorityEnum;
use Core\Domains\HelpDesk\Enums\TicketStatusEnum;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\HelpDesk\Models\TicketEntity;
use Core\Domains\HelpDesk\Services\FileService;
use Core\Domains\HelpDesk\Services\TicketCatalogService;
use Core\Domains\HelpDesk\Services\TicketCategoryService;
use Core\Domains\HelpDesk\Services\TicketService;
use Core\Exceptions\ValidationException;

readonly class UpdateCommand
{
    public function __construct(
        private TicketService         $ticketService,
        private TicketCategoryService $categoryService,
        private TicketCatalogService  $serviceService,
        private FileService           $fileService,
        private UpdateValidator       $validator,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function execute(UpdateInput $input): TicketEntity
    {
        $this->validator->validate($input);

        $category = $this->categoryService->getById($input->categoryId);
        $service  = $this->serviceService->getById($input->serviceId);

        $ticket = (new TicketEntity())
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

        $status = $ticket->getStatus();
        if ($ticket->getResult() || $status?->isClosed() || $status?->isRejected()) {
            $ticket->setResolvedAt(Carbon::now());
        }
        else {
            $ticket->setResolvedAt(null);
        }

        $ticket = $this->ticketService->save($ticket);
        $this->fileService->storeAndSave($input->files, $ticket->getId());
        $this->fileService->storeAndSave($input->resultFiles, $ticket->getId(), FileTypeEnum::TICKET_RESULT);

        return $ticket;
    }
}
