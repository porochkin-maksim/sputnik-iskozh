<?php declare(strict_types=1);

namespace Core\App\HelpDesk\Ticket;

use Core\Contracts\EventDispatcherInterface;
use Core\Domains\HelpDesk\Enums\TicketPriorityEnum;
use Core\Domains\HelpDesk\Enums\TicketStatusEnum;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\HelpDesk\Events\TicketCreated;
use Core\Domains\HelpDesk\Models\TicketEntity;
use Core\Domains\HelpDesk\Services\FileService;
use Core\Domains\HelpDesk\Services\TicketCatalogService;
use Core\Domains\HelpDesk\Services\TicketCategoryService;
use Core\Domains\HelpDesk\Services\TicketService;
use Core\Exceptions\ValidationException;
use InvalidArgumentException;

readonly class CreateCommand
{
    public function __construct(
        private TicketService            $ticketService,
        private TicketCategoryService    $categoryService,
        private TicketCatalogService     $serviceService,
        private FileService              $fileService,
        private CreateValidator          $validator,
        private EventDispatcherInterface $eventDispatcher,
    )
    {
    }

    /**
     * @throws ValidationException
     * @throws InvalidArgumentException
     */
    public function execute(CreateInput $input): TicketEntity
    {
        $this->validator->validate($input);

        $type     = TicketTypeEnum::byCode($input->typeCode);
        $category = $this->categoryService->findByTypeAndCode($type, $input->categoryCode);
        $service  = $this->serviceService->findByCategoryIdAndCode($category?->getId(), $input->serviceCode);

        $ticket = (new TicketEntity())
            ->setType($type)
            ->setCategoryId($category?->getId())
            ->setServiceId($service?->getId())
            ->setStatus(TicketStatusEnum::NEW)
            ->setPriority(TicketPriorityEnum::MEDIUM)
            ->setDescription($input->description)
            ->setContactName($input->contactName)
            ->setContactEmail($input->contactEmail)
            ->setContactPhone($input->contactPhone)
        ;

        if ($input->accountId) {
            $ticket->setAccountId($input->accountId);
        }
        if ($input->userId) {
            $ticket->setUserId($input->userId);
        }

        $ticket = $this->ticketService->save($ticket);
        $this->fileService->storeAndSave($input->files, $ticket->getId());
        $this->eventDispatcher->dispatch(new TicketCreated($ticket->getId()));

        return $ticket;
    }
}
