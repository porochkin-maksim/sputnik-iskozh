<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\UseCases\Category;

use Core\Domains\HelpDesk\HelpDeskServiceLocator;
use Core\Domains\HelpDesk\Models\TicketCategoryDTO;
use Core\Domains\HelpDesk\Searchers\TicketSearcher;
use Core\Domains\HelpDesk\Services\TicketCategoryService;
use Core\Domains\HelpDesk\Searchers\TicketServiceSearcher;
use Core\Domains\HelpDesk\Services\TicketService;
use Core\Domains\HelpDesk\Services\TicketServiceService;
use RuntimeException;

readonly class DeleteUseCase
{
    private TicketCategoryService $ticketCategoryService;
    private TicketServiceService  $ticketServiceService;
    private TicketService         $ticketService;

    public function __construct()
    {
        $this->ticketCategoryService = HelpDeskServiceLocator::TicketCategoryService();
        $this->ticketServiceService  = HelpDeskServiceLocator::TicketServiceService();
        $this->ticketService         = HelpDeskServiceLocator::TicketService();
    }

    /**
     * @throws RuntimeException
     */
    public function execute(TicketCategoryDTO $dto): void
    {
        // Проверяем, есть ли услуги у категории
        $services = $this->ticketServiceService->search(
            new TicketServiceSearcher()->setCategoryId($dto->getId()),
        );

        if ($services->getTotal() > 0) {
            throw new RuntimeException(
                sprintf('Невозможно удалить категорию "%s", так как у неё есть связанные услуги', $dto->getName()),
            );
        }

        // Проверяем, есть ли услуги у категории
        $services = $this->ticketService->search(
            new TicketSearcher()->setCategoryId($dto->getId()),
        );

        if ($services->getTotal() > 0) {
            throw new RuntimeException(
                sprintf('Невозможно удалить категорию "%s", так как у неё есть связанные завки', $dto->getName()),
            );
        }

        $this->ticketCategoryService->deleteById($dto->getId());
    }
}
