<?php declare(strict_types=1);

namespace App\Repositories\HelpDesk;

use App\Models\HelpDesk\TicketComment;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Domains\HelpDesk\Collection\TicketCommentCollection;
use Core\Domains\HelpDesk\Models\TicketCommentEntity;
use Core\Domains\HelpDesk\Responses\TicketCommentSearchResponse;
use Core\Domains\HelpDesk\Searchers\TicketCommentSearcher;
use Core\Domains\HelpDesk\TicketCommentRepositoryInterface;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Repositories\SearcherInterface;
use Core\Shared\Collections\Collection;
use ReturnTypeWillChange;

class TicketCommentEloquentRepository implements TicketCommentRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly TicketCommentEloquentMapper $mapper,
    )
    {
    }

    protected function modelClass(): string
    {
        return TicketComment::class;
    }

    protected function getTable(): string
    {
        return TicketComment::TABLE;
    }

    protected function getMapper(): RepositoryDataMapperInterface
    {
        return $this->mapper;
    }

    protected function getEmptyCollection(): Collection
    {
        return new TicketCommentCollection();
    }

    #[ReturnTypeWillChange]
    /**
     * @return TicketCommentSearchResponse
     */
    protected function getEmptySearchResponse(): TicketCommentSearchResponse
    {
        return new TicketCommentSearchResponse();
    }

    #[ReturnTypeWillChange]
    /**
     * @return TicketCommentSearcher
     */
    protected function getEmptySearcher(): SearcherInterface
    {
        return new TicketCommentSearcher();
    }

    public function search(SearcherInterface $searcher): TicketCommentSearchResponse
    {
        return $this->searchModels($searcher);
    }

    public function save(TicketCommentEntity $comment): TicketCommentEntity
    {
        /** @var TicketComment|null $model */
        $model = $this->getModelById($comment->getId());
        /** @var TicketComment $model */
        $model = $this->mapper->makeRepositoryDataFromEntity($comment, $model);
        $model->save();

        return $this->mapper->makeEntityFromRepositoryData($model);
    }

    public function getById(?int $id): ?TicketCommentEntity
    {
        /** @var TicketComment|null $model */
        $model = $this->getModelById($id);

        return $model ? $this->mapper->makeEntityFromRepositoryData($model) : null;
    }
}
