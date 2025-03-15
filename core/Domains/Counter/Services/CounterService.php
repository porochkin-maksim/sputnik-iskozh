<?php declare(strict_types=1);

namespace Core\Domains\Counter\Services;

use App\Models\Counter\Counter;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Counter\Collections\CounterCollection;
use Core\Domains\Counter\Factories\CounterFactory;
use Core\Domains\Counter\Models\CounterDTO;
use Core\Domains\Counter\Models\CounterSearcher;
use Core\Domains\Counter\Repositories\CounterRepository;
use Core\Domains\Counter\Responses\CounterSearchResponse;

readonly class CounterService
{
    public function __construct(
        private CounterFactory    $counterFactory,
        private CounterRepository $counterRepository,
    )
    {
    }

    public function search(CounterSearcher $searcher): CounterSearchResponse
    {
        $response = $this->counterRepository->search($searcher);

        $result = new CounterSearchResponse();
        $result->setTotal($response->getTotal());

        $collection = new CounterCollection();
        foreach ($response->getItems() as $item) {
            $collection->add($this->counterFactory->makeDtoFromObject($item));
        }

        return $result->setItems($collection);
    }

    public function getById(int $id): ?CounterDTO
    {
        $searcher = new CounterSearcher();
        $searcher->setId($id);

        return $this->search($searcher)->getItems()->first();
    }

    public function save(CounterDTO $dto): CounterDTO
    {
        $counter = null;

        if ($dto->getId()) {
            $counter = $this->counterRepository->getById($dto->getId());
        }
        $counters = $this->getByAccountId($dto->getAccountId());
        $dto->setIsInvoicing(! $counters->getInvoicing()->count());

        $counter = $this->counterFactory->makeModelFromDto($dto, $counter);
        $counter = $this->counterRepository->save($counter);

        return $this->counterFactory->makeDtoFromObject($counter);
    }

    public function getByAccountId(?int $id): CounterCollection
    {
        $searcher = new CounterSearcher();//
        $searcher
            ->setAccountId($id)
            ->setSortOrderProperty(Counter::IS_INVOICING, SearcherInterface::SORT_ORDER_DESC)
            ->setSortOrderProperty(Counter::ID, SearcherInterface::SORT_ORDER_DESC)
        ;

        return $this->search($searcher)->getItems();
    }
}
