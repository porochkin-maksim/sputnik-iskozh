<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Services;

use Core\Domains\HelpDesk\Collection\TicketServiceCollection;
use Core\Domains\HelpDesk\Models\TicketServiceDTO;
use Core\Domains\HelpDesk\Repositories\TicketServiceRepository;
use Core\Domains\HelpDesk\Responses\TicketServiceSearchResponse;
use Core\Domains\HelpDesk\Searchers\TicketServiceSearcher;

readonly class TicketServiceService
{
    public function __construct(
        private TicketServiceRepository $ticketServiceRepository,
    )
    {
    }

    public function search(?TicketServiceSearcher $searcher = null): TicketServiceSearchResponse
    {
        return $this->ticketServiceRepository->search($searcher ? : new TicketServiceSearcher());
    }

    public function getById(?int $id): ?TicketServiceDTO
    {
        return $this->ticketServiceRepository->getById($id);
    }

    public function save(TicketServiceDTO $service): TicketServiceDTO
    {
        return $this->ticketServiceRepository->save($service);
    }

    public function deleteById(?int $id): bool
    {
        return $this->ticketServiceRepository->deleteById($id);
    }

    public function getByCategoryId(int $categoryId): TicketServiceCollection
    {
        return $this->search(
            new TicketServiceSearcher()
                ->setCategoryId($categoryId),
        )->getItems();
    }

    public function findByCategoryIdAndCode(int $categoryId, string $code): ?TicketServiceDTO
    {
        return $this->search(
            new TicketServiceSearcher()
                ->setCategoryId($categoryId)
                ->setCode($code),
        )->getItems()
            ->first()
        ;
    }
}
