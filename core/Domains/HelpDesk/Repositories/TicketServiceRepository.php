<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Repositories;

use App\Models\HelpDesk\TicketService;
use Core\Db\RepositoryTrait;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\HelpDesk\Collection\TicketServiceCollection;
use Core\Domains\HelpDesk\Models\TicketServiceDTO;
use Core\Domains\HelpDesk\Factories\TicketServiceFactory;
use Core\Domains\HelpDesk\Responses\TicketServiceSearchResponse;

class TicketServiceRepository
{
    use RepositoryTrait;

    private const string TABLE = TicketService::TABLE;

    public function __construct(
        private TicketServiceFactory $ticketServiceFactory,
    )
    {
    }

    protected function modelClass(): string
    {
        return TicketService::class;
    }

    public function search(SearcherInterface $searcher): TicketServiceSearchResponse
    {
        $response   = $this->searchModels($searcher);
        $collection = new TicketServiceCollection();
        foreach ($response->getItems() as $model) {
            $collection->add($this->ticketServiceFactory->makeDtoFromObject($model));
        }

        $result = new TicketServiceSearchResponse();
        $result->setTotal($response->getTotal())
            ->setItems($collection)
        ;

        return $result;
    }

    public function save(TicketServiceDTO $dto): TicketServiceDTO
    {
        $model = $this->getModelById($dto->getId());
        $model = $this->ticketServiceFactory->makeModelFromDto($dto, $model);
        $model->save();

        return $this->ticketServiceFactory->makeDtoFromObject($model);
    }

    public function getById(?int $id): ?TicketServiceDTO
    {
        /** @var TicketService $model */
        $model = $this->getModelById($id);

        return $model ? $this->ticketServiceFactory->makeDtoFromObject($model) : null;
    }
}
