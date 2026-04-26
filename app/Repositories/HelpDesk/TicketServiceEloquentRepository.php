<?php declare(strict_types=1);

namespace App\Repositories\HelpDesk;

use App\Models\HelpDesk\TicketService;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Domains\HelpDesk\Collection\TicketServiceCollection;
use Core\Domains\HelpDesk\Models\TicketServiceEntity;
use Core\Domains\HelpDesk\Responses\TicketServiceSearchResponse;
use Core\Domains\HelpDesk\Searchers\TicketServiceSearcher;
use Core\Domains\HelpDesk\TicketServiceRepositoryInterface;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Repositories\SearcherInterface;
use Core\Shared\Collections\Collection;
use ReturnTypeWillChange;

class TicketServiceEloquentRepository implements TicketServiceRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly TicketServiceEloquentMapper $mapper,
    )
    {
    }

    protected function modelClass(): string
    {
        return TicketService::class;
    }

    protected function getTable(): string
    {
        return TicketService::TABLE;
    }

    protected function getMapper(): RepositoryDataMapperInterface
    {
        return $this->mapper;
    }

    protected function getEmptyCollection(): Collection
    {
        return new TicketServiceCollection();
    }

    #[ReturnTypeWillChange]
    /**
     * @return TicketServiceSearchResponse
     */
    protected function getEmptySearchResponse(): TicketServiceSearchResponse
    {
        return new TicketServiceSearchResponse();
    }

    #[ReturnTypeWillChange]
    /**
     * @return TicketServiceSearcher
     */
    protected function getEmptySearcher(): SearcherInterface
    {
        return new TicketServiceSearcher();
    }

    public function search(SearcherInterface $searcher): TicketServiceSearchResponse
    {
        return $this->searchModels($searcher);
    }

    public function save(TicketServiceEntity $service): TicketServiceEntity
    {
        /** @var TicketService|null $model */
        $model = $this->getModelById($service->getId());
        /** @var TicketService $model */
        $model = $this->mapper->makeRepositoryDataFromEntity($service, $model);
        $model->save();

        return $this->mapper->makeEntityFromRepositoryData($model);
    }

    public function getById(?int $id): ?TicketServiceEntity
    {
        /** @var TicketService|null $model */
        $model = $this->getModelById($id);

        return $model ? $this->mapper->makeEntityFromRepositoryData($model) : null;
    }
}
