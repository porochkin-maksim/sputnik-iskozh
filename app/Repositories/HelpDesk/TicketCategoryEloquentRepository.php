<?php declare(strict_types=1);

namespace App\Repositories\HelpDesk;

use App\Models\HelpDesk\TicketCategory;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Repositories\RepositoryConfig;
use Core\Contracts\RepositoryDataMapperInterface;
use Core\Domains\HelpDesk\Collection\TicketCategoryCollection;
use Core\Domains\HelpDesk\Models\TicketCategoryEntity;
use Core\Domains\HelpDesk\Responses\TicketCategorySearchResponse;
use Core\Domains\HelpDesk\Searchers\TicketCategorySearcher;
use Core\Domains\HelpDesk\TicketCategoryRepositoryInterface;
use Core\Repositories\SearcherInterface;

class TicketCategoryEloquentRepository implements TicketCategoryRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly TicketCategoryEloquentMapper $mapper,
    )
    {
    }

    protected function repositoryConfig(): RepositoryConfig
    {
        return new RepositoryConfig(
            modelClass         : TicketCategory::class,
            table              : TicketCategory::TABLE,
            collectionClass    : TicketCategoryCollection::class,
            searchResponseClass: TicketCategorySearchResponse::class,
            searcherClass      : TicketCategorySearcher::class,
        );
    }

    protected function getMapper(): ?RepositoryDataMapperInterface
    {
        return $this->mapper;
    }

    public function search(SearcherInterface $searcher): TicketCategorySearchResponse
    {
        return $this->searchModels($searcher);
    }

    public function save(TicketCategoryEntity $category): TicketCategoryEntity
    {
        /** @var TicketCategory|null $model */
        $model = $this->getModelById($category->getId());
        /** @var TicketCategory $model */
        $model = $this->mapper->makeRepositoryDataFromEntity($category, $model);
        $model->save();

        return $this->mapper->makeEntityFromRepositoryData($model);
    }

    public function getById(?int $id): ?TicketCategoryEntity
    {
        /** @var TicketCategory|null $model */
        $model = $this->getModelById($id);

        return $model ? $this->mapper->makeEntityFromRepositoryData($model) : null;
    }
}
