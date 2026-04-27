<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\UseCases\Ticket;

use Core\Domains\HelpDesk\Enums\TicketPriorityEnum;
use Core\Domains\HelpDesk\Enums\TicketStatusEnum;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\HelpDesk\HelpDeskServiceLocator;
use Core\Domains\HelpDesk\Jobs\NotifyAboutNewTicketJob;
use Core\Domains\HelpDesk\Models\TicketDTO;
use Core\Domains\HelpDesk\Services\FileService;
use Core\Domains\HelpDesk\Services\TicketCategoryService;
use Core\Domains\HelpDesk\Services\TicketService;
use Core\Domains\HelpDesk\Services\TicketServiceService;
use Illuminate\Validation\ValidationException;

readonly class CreateUseCase
{
    private TicketService         $ticketService;
    private TicketCategoryService $categoryService;
    private TicketServiceService  $serviceService;
    private FileService     $fileService;
    private CreateValidator $validator;

    public function __construct()
    {
        $this->ticketService   = HelpDeskServiceLocator::TicketService();
        $this->categoryService = HelpDeskServiceLocator::TicketCategoryService();
        $this->serviceService  = HelpDeskServiceLocator::TicketServiceService();
        $this->fileService     = HelpDeskServiceLocator::FileService();
        $this->validator       = new CreateValidator();
    }

    /**
     * @throws ValidationException
     * @throws \InvalidArgumentException
     */
    public function execute(CreateInputDTO $input): TicketDTO
    {
        $this->validator->validate($input);

        $type     = TicketTypeEnum::byCode($input->typeCode);
        $category = $this->categoryService->findByTypeAndCode($type, $input->categoryCode);
        $service  = $this->serviceService->findByCategoryIdAndCode($category?->getId(), $input->serviceCode);


        $ticketDto = new TicketDTO()
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
            $ticketDto->setAccountId($input->accountId);
        }
        if ($input->userId) {
            $ticketDto->setUserId($input->userId);
        }

        $ticket = $this->ticketService->save($ticketDto);

        foreach ($input->files as $file) {
            $this->fileService->store($file, $ticket->getId());
        }

        dispatch(new NotifyAboutNewTicketJob($ticket->getId()));

        return $ticket;
    }
}
