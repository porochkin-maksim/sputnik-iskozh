<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Repositories;

use App\Models\HelpDesk\TicketComment;
use Core\Db\RepositoryTrait;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\HelpDesk\Collection\TicketCommentCollection;
use Core\Domains\HelpDesk\Models\TicketCommentDTO;
use Core\Domains\HelpDesk\Factories\TicketCommentFactory;
use Core\Domains\HelpDesk\Responses\TicketCommentSearchResponse;

class TicketCommentRepository
{
    use RepositoryTrait;

    private const string TABLE = TicketComment::TABLE;

    public function __construct(
        private TicketCommentFactory $ticketCommentFactory,
    )
    {
    }

    protected function modelClass(): string
    {
        return TicketComment::class;
    }

    public function search(SearcherInterface $searcher): TicketCommentSearchResponse
    {
        $response   = $this->searchModels($searcher);
        $collection = new TicketCommentCollection();
        foreach ($response->getItems() as $model) {
            $collection->add($this->ticketCommentFactory->makeDtoFromObject($model));
        }

        $result = new TicketCommentSearchResponse();
        $result->setTotal($response->getTotal())
            ->setItems($collection)
        ;

        return $result;
    }

    public function save(TicketCommentDTO $dto): TicketCommentDTO
    {
        $model = $this->getModelById($dto->getId());
        $model = $this->ticketCommentFactory->makeModelFromDto($dto, $model);
        $model->save();

        return $this->ticketCommentFactory->makeDtoFromObject($model);
    }

    public function getById(?int $id): ?TicketCommentDTO
    {
        /** @var TicketComment $model */
        $model = $this->getModelById($id);

        return $model ? $this->ticketCommentFactory->makeDtoFromObject($model) : null;
    }
}
