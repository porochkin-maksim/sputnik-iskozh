<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Services;

use Core\Domains\HelpDesk\Models\TicketServiceEntity;
use Core\Domains\HelpDesk\Responses\TicketServiceSearchResponse;
use Core\Domains\HelpDesk\Searchers\TicketServiceSearcher;
use Core\Domains\HelpDesk\TicketServiceRepositoryInterface;

readonly class TicketCatalogService
{
    public function __construct(
        private TicketServiceRepositoryInterface $ticketServiceRepository,
    )
    {
    }

    public function search(?TicketServiceSearcher $searcher = null): TicketServiceSearchResponse
    {
        return $this->ticketServiceRepository->search($searcher ? : new TicketServiceSearcher());
    }

    public function getById(?int $id): ?TicketServiceEntity
    {
        return $this->ticketServiceRepository->getById($id);
    }

    public function save(TicketServiceEntity $service): TicketServiceEntity
    {
        return $this->ticketServiceRepository->save($service);
    }

    public function deleteById(?int $id): bool
    {
        return $this->ticketServiceRepository->deleteById($id);
    }

    public function findByCategoryIdAndCode(int $categoryId, string $code): ?TicketServiceEntity
    {
        $searcher = new TicketServiceSearcher();
        $searcher
            ->setCategoryId($categoryId)
            ->setCode($code)
        ;

        return $this->search($searcher)->getItems()->first();
    }
}
