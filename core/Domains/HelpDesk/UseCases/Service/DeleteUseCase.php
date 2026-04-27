<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\UseCases\Service;

use Core\Domains\HelpDesk\HelpDeskServiceLocator;
use Core\Domains\HelpDesk\Models\TicketServiceDTO;
use Core\Domains\HelpDesk\Searchers\TicketSearcher;
use Core\Domains\HelpDesk\Services\TicketService;
use Core\Domains\HelpDesk\Services\TicketServiceService;
use RuntimeException;

readonly class DeleteUseCase
{
    private TicketServiceService $ticketServiceService;
    private TicketService        $ticketService;

    public function __construct()
    {
        $this->ticketServiceService = HelpDeskServiceLocator::TicketServiceService();
        $this->ticketService        = HelpDeskServiceLocator::TicketService();
    }

    /**
     * @throws RuntimeException
     */
    public function execute(TicketServiceDTO $dto): void
    {
        // Проверяем, есть ли по услуге заявки
        $services = $this->ticketService->search(
            new TicketSearcher()->setServiceId($dto->getId()),
        );

        if ($services->getTotal() > 0) {
            throw new \RuntimeException(
                sprintf('Невозможно удалить услугу "%s", так как у неё есть связанные завки', $dto->getName()),
            );
        }

        $this->ticketServiceService->deleteById($dto->getId());
    }
}
