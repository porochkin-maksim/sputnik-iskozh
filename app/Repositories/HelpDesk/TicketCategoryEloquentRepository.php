<?php declare(strict_types=1);

namespace App\Repositories\HelpDesk;

use App\Models\HelpDesk\TicketCategory;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Domains\HelpDesk\Collection\TicketCategoryCollection;
use Core\Domains\HelpDesk\Models\TicketCategoryEntity;
use Core\Domains\HelpDesk\Responses\TicketCategorySearchResponse;
use Core\Domains\HelpDesk\Searchers\TicketCategorySearcher;
use Core\Domains\HelpDesk\TicketCategoryRepositoryInterface;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Repositories\SearcherInterface;
use Core\Shared\Collections\Collection;
use ReturnTypeWillChange;

class TicketCategoryEloquentRepository implements TicketCategoryRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly TicketCategoryEloquentMapper $mapper,
    )
    {
    }

    protected function modelClass(): string
    {
        return TicketCategory::class;
    }

    protected function getTable(): string
    {
        return TicketCategory::TABLE;
    }

    protected function getMapper(): RepositoryDataMapperInterface
    {
        return $this->mapper;
    }

    protected function getEmptyCollection(): Collection
    {
        return new TicketCategoryCollection();
    }

    #[ReturnTypeWillChange]
    /**
     * @return TicketCategorySearchResponse
     */
    protected function getEmptySearchResponse(): TicketCategorySearchResponse
    {
        return new TicketCategorySearchResponse();
    }

    #[ReturnTypeWillChange]
    /**
     * @return TicketCategorySearcher
     */
    protected function getEmptySearcher(): SearcherInterface
    {
        return new TicketCategorySearcher();
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
