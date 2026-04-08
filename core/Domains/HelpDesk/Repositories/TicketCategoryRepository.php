<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Repositories;

use App\Models\HelpDesk\TicketCategory;
use Core\Db\RepositoryTrait;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\HelpDesk\Collection\TicketCategoryCollection;
use Core\Domains\HelpDesk\Models\TicketCategoryDTO;
use Core\Domains\HelpDesk\Factories\TicketCategoryFactory;
use Core\Domains\HelpDesk\Responses\TicketCategorySearchResponse;

class TicketCategoryRepository
{
    use RepositoryTrait;

    private const string TABLE = TicketCategory::TABLE;

    public function __construct(
        private TicketCategoryFactory $ticketCategoryFactory,
    )
    {
    }

    protected function modelClass(): string
    {
        return TicketCategory::class;
    }

    public function search(SearcherInterface $searcher): TicketCategorySearchResponse
    {
        $response   = $this->searchModels($searcher);
        $collection = new TicketCategoryCollection();
        foreach ($response->getItems() as $model) {
            $collection->add($this->ticketCategoryFactory->makeDtoFromObject($model));
        }

        $result = new TicketCategorySearchResponse();
        $result->setTotal($response->getTotal())
            ->setItems($collection)
        ;

        return $result;
    }

    public function save(TicketCategoryDTO $dto): TicketCategoryDTO
    {
        $model = $this->getModelById($dto->getId());
        $model = $this->ticketCategoryFactory->makeModelFromDto($dto, $model);
        $model->save();

        return $this->ticketCategoryFactory->makeDtoFromObject($model);
    }

    public function getById(?int $id): ?TicketCategoryDTO
    {
        /** @var TicketCategory $model */
        $model = $this->getModelById($id);

        return $model ? $this->ticketCategoryFactory->makeDtoFromObject($model) : null;
    }
}
