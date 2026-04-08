<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Repositories;

use App\Models\HelpDesk\Ticket;
use Core\Db\RepositoryTrait;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\HelpDesk\Collection\TicketCollection;
use Core\Domains\HelpDesk\Factories\TicketFactory;
use Core\Domains\HelpDesk\Models\TicketDTO;
use Core\Domains\HelpDesk\Responses\TicketSearchResponse;
use Core\Domains\HelpDesk\Searchers\TicketSearcher;

class TicketRepository
{
    use RepositoryTrait;

    private const string TABLE = Ticket::TABLE;

    public function __construct(
        private TicketFactory $ticketFactory,
    ) { }

    protected function modelClass(): string
    {
        return Ticket::class;
    }

    public function search(SearcherInterface $searcher): TicketSearchResponse
    {
        $response   = $this->searchModels($searcher);
        $collection = new TicketCollection();
        foreach ($response->getItems() as $model) {
            $collection->add($this->ticketFactory->makeDtoFromObject($model));
        }

        $result = new TicketSearchResponse();
        $result->setTotal($response->getTotal())
            ->setItems($collection)
        ;

        return $result;
    }

    public function save(TicketDTO $dto): TicketDTO
    {
        $model = $this->getModelById($dto->getId());
        $model = $this->ticketFactory->makeModelFromDto($dto, $model);
        $model->save();

        return $this->ticketFactory->makeDtoFromObject($model);
    }

    public function getById(?int $id): ?TicketDTO
    {
        /** @var Ticket $model */
        $model = $this->getModelById($id, new TicketSearcher());

        return $model ? $this->ticketFactory->makeDtoFromObject($model) : null;
    }
}
