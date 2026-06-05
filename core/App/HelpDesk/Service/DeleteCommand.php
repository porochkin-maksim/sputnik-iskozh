<?php declare(strict_types=1);

namespace Core\App\HelpDesk\Service;

use Core\Domains\HelpDesk\Models\TicketServiceEntity;
use Core\Domains\HelpDesk\Searchers\TicketSearcher;
use Core\Domains\HelpDesk\Services\TicketCatalogService;
use Core\Domains\HelpDesk\Services\TicketService;
use RuntimeException;

readonly class DeleteCommand
{
    public function __construct(
        private TicketCatalogService $ticketServiceService,
        private TicketService        $ticketService,
    )
    {
    }

    /**
     * @throws RuntimeException
     */
    public function execute(TicketServiceEntity $service): void
    {
        $ticketSearcher = new TicketSearcher();
        $ticketSearcher->setServiceId($service->getId());
        $tickets = $this->ticketService->search($ticketSearcher);

        if ($tickets->getTotal() > 0) {
            throw new RuntimeException(
                sprintf('Невозможно удалить услугу "%s", так как у неё есть связанные завки', $service->getName()),
            );
        }

        $this->ticketServiceService->deleteById($service->getId());
    }
}
