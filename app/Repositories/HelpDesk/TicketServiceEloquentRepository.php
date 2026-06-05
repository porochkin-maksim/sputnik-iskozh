<?php declare(strict_types=1);

namespace App\Repositories\HelpDesk;

use App\Models\HelpDesk\TicketService;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Contracts\RepositoryDataMapperInterface;
use Core\Domains\HelpDesk\Collection\TicketServiceCollection;
use Core\Domains\HelpDesk\Models\TicketServiceEntity;
use Core\Domains\HelpDesk\Responses\TicketServiceSearchResponse;
use Core\Domains\HelpDesk\Searchers\TicketServiceSearcher;
use Core\Domains\HelpDesk\TicketServiceRepositoryInterface;
use Core\Repositories\RepositoryConfig;
use Core\Repositories\SearcherInterface;

class TicketServiceEloquentRepository implements TicketServiceRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly TicketServiceEloquentMapper $mapper,
    )
    {
    }

    protected function repositoryConfig(): RepositoryConfig
    {
        return new RepositoryConfig(
            modelClass         : TicketService::class,
            table              : TicketService::TABLE,
            collectionClass    : TicketServiceCollection::class,
            searchResponseClass: TicketServiceSearchResponse::class,
            searcherClass      : TicketServiceSearcher::class,
        );
    }

    protected function getMapper(): RepositoryDataMapperInterface
    {
        return $this->mapper;
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
