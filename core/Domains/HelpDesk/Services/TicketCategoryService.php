<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Services;

use Core\Domains\HelpDesk\Collection\TicketCategoryCollection;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\HelpDesk\Models\TicketCategoryEntity;
use Core\Domains\HelpDesk\Responses\TicketCategorySearchResponse;
use Core\Domains\HelpDesk\Searchers\TicketCategorySearcher;
use Core\Domains\HelpDesk\TicketCategoryRepositoryInterface;

readonly class TicketCategoryService
{
    public function __construct(
        private TicketCategoryRepositoryInterface $ticketCategoryRepository,
    )
    {
    }

    public function search(?TicketCategorySearcher $searcher = null): TicketCategorySearchResponse
    {
        return $this->ticketCategoryRepository->search($searcher ? : new TicketCategorySearcher());
    }

    public function getById(?int $id): ?TicketCategoryEntity
    {
        return $this->ticketCategoryRepository->getById($id);
    }

    public function save(TicketCategoryEntity $category): TicketCategoryEntity
    {
        return $this->ticketCategoryRepository->save($category);
    }

    public function deleteById(?int $id): bool
    {
        return $this->ticketCategoryRepository->deleteById($id);
    }

    public function getByType(TicketTypeEnum $type, bool $activeOnly = true): TicketCategoryCollection
    {
        $seearcher = new TicketCategorySearcher();
        $seearcher
            ->setType($type)
            ->setWithServices();

        if ($activeOnly) {
            $seearcher->setActive(true);
        }

        return $this->search($seearcher)->getItems();
    }

    public function findByTypeAndCode(TicketTypeEnum $type, string $code): ?TicketCategoryEntity
    {
        $searcher = new TicketCategorySearcher();
        $searcher
            ->setType($type)
            ->setCode($code);

        return $this->search($searcher)->getItems()->first();
    }
}
