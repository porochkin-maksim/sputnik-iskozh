<?php declare(strict_types=1);

namespace App\Repositories\HelpDesk;

use App\Models\HelpDesk\Ticket;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Repositories\RepositoryConfig;
use Core\Contracts\RepositoryDataMapperInterface;
use Core\Domains\HelpDesk\Collection\TicketCollection;
use Core\Domains\HelpDesk\Models\TicketEntity;
use Core\Domains\HelpDesk\Responses\TicketSearchResponse;
use Core\Domains\HelpDesk\Searchers\TicketSearcher;
use Core\Domains\HelpDesk\TicketRepositoryInterface;
use Core\Repositories\SearcherInterface;

class TicketEloquentRepository implements TicketRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly TicketEloquentMapper $mapper,
    )
    {
    }

    protected function repositoryConfig(): RepositoryConfig
    {
        return new RepositoryConfig(
            modelClass         : Ticket::class,
            table              : Ticket::TABLE,
            collectionClass    : TicketCollection::class,
            searchResponseClass: TicketSearchResponse::class,
            searcherClass      : TicketSearcher::class,
        );
    }

    protected function getMapper(): ?RepositoryDataMapperInterface
    {
        return $this->mapper;
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
