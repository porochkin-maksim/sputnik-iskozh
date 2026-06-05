<?php declare(strict_types=1);

namespace App\Repositories\HelpDesk;

use App\Models\HelpDesk\TicketComment;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Repositories\RepositoryConfig;
use Core\Contracts\RepositoryDataMapperInterface;
use Core\Domains\HelpDesk\Collection\TicketCommentCollection;
use Core\Domains\HelpDesk\Models\TicketCommentEntity;
use Core\Domains\HelpDesk\Responses\TicketCommentSearchResponse;
use Core\Domains\HelpDesk\Searchers\TicketCommentSearcher;
use Core\Domains\HelpDesk\TicketCommentRepositoryInterface;
use Core\Repositories\SearcherInterface;

class TicketCommentEloquentRepository implements TicketCommentRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly TicketCommentEloquentMapper $mapper,
    )
    {
    }

    protected function repositoryConfig(): RepositoryConfig
    {
        return new RepositoryConfig(
            modelClass         : TicketComment::class,
            table              : TicketComment::TABLE,
            collectionClass    : TicketCommentCollection::class,
            searchResponseClass: TicketCommentSearchResponse::class,
            searcherClass      : TicketCommentSearcher::class,
        );
    }

    protected function getMapper(): ?RepositoryDataMapperInterface
    {
        return $this->mapper;
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
