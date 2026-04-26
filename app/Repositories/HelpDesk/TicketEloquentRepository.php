<?php declare(strict_types=1);

namespace App\Repositories\HelpDesk;

use App\Models\HelpDesk\Ticket;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Domains\HelpDesk\Collection\TicketCollection;
use Core\Domains\HelpDesk\Models\TicketEntity;
use Core\Domains\HelpDesk\Responses\TicketSearchResponse;
use Core\Domains\HelpDesk\Searchers\TicketSearcher;
use Core\Domains\HelpDesk\TicketRepositoryInterface;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Repositories\SearcherInterface;
use Core\Shared\Collections\Collection;
use ReturnTypeWillChange;

class TicketEloquentRepository implements TicketRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly TicketEloquentMapper $mapper,
    )
    {
    }

    protected function modelClass(): string
    {
        return Ticket::class;
    }

    protected function getTable(): string
    {
        return Ticket::TABLE;
    }

    protected function getMapper(): RepositoryDataMapperInterface
    {
        return $this->mapper;
    }

    protected function getEmptyCollection(): Collection
    {
        return new TicketCollection();
    }

    #[ReturnTypeWillChange]
    /**
     * @return TicketSearchResponse
     */
    protected function getEmptySearchResponse(): TicketSearchResponse
    {
        return new TicketSearchResponse();
    }

    #[ReturnTypeWillChange]
    /**
     * @return TicketSearcher
     */
    protected function getEmptySearcher(): SearcherInterface
    {
        return new TicketSearcher();
    }

    public function search(SearcherInterface $searcher): TicketSearchResponse
    {
        return $this->searchModels($searcher);
    }

    public function save(TicketEntity $ticket): TicketEntity
    {
        /** @var Ticket|null $model */
        $model = $this->getModelById($ticket->getId());
        /** @var Ticket $model */
        $model = $this->mapper->makeRepositoryDataFromEntity($ticket, $model);
        $model->save();

        return $this->mapper->makeEntityFromRepositoryData($model);
    }

    public function getById(?int $id): ?TicketEntity
    {
        /** @var Ticket|null $model */
        $model = $this->getModelById($id);

        return $model ? $this->mapper->makeEntityFromRepositoryData($model) : null;
    }
}
