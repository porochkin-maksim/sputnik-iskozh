<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Services;

use Core\Domains\HelpDesk\Collection\TicketCategoryCollection;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\HelpDesk\Models\TicketCategoryDTO;
use Core\Domains\HelpDesk\Repositories\TicketCategoryRepository;
use Core\Domains\HelpDesk\Responses\TicketCategorySearchResponse;
use Core\Domains\HelpDesk\Searchers\TicketCategorySearcher;

readonly class TicketCategoryService
{
    public function __construct(
        private TicketCategoryRepository $ticketCategoryRepository,
    )
    {
    }

    public function search(?TicketCategorySearcher $searcher = null): TicketCategorySearchResponse
    {
        return $this->ticketCategoryRepository->search($searcher ? : new TicketCategorySearcher());
    }

    public function getById(?int $id): ?TicketCategoryDTO
    {
        return $this->ticketCategoryRepository->getById($id);
    }

    public function save(TicketCategoryDTO $category): TicketCategoryDTO
    {
        return $this->ticketCategoryRepository->save($category);
    }

    public function deleteById(?int $id): bool
    {
        return $this->ticketCategoryRepository->deleteById($id);
    }

    public function getByType(TicketTypeEnum $type, bool $activeOnly = true): TicketCategoryCollection
    {
        $seearcher = new TicketCategorySearcher()
            ->setType($type)
            ->setWithServices()
        ;

        if ($activeOnly) {
            $seearcher->setActive(true);
        }

        return $this->search($seearcher)->getItems();
    }

    public function findByTypeAndCode(TicketTypeEnum $type, string $code): ?TicketCategoryDTO
    {
        return $this->search(
            new TicketCategorySearcher()
                ->setType($type)
                ->setCode($code),
        )->getItems()->first();
    }
}
