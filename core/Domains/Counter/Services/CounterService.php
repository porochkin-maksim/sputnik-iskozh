<?php declare(strict_types=1);

namespace Core\Domains\Counter\Services;

use App\Models\Counter\Counter;
use Core\Domains\Counter\Collections\CounterCollection;
use Core\Domains\Counter\Models\CounterDTO;
use Core\Domains\Counter\Models\CounterSearcher;
use Core\Domains\Counter\Repositories\CounterRepository;
use Core\Domains\Counter\Responses\CounterSearchResponse;
use Core\Db\Searcher\SearcherInterface;

readonly class CounterService
{
    public function __construct(
        private CounterRepository $counterRepository,
    )
    {
    }

    public function search(CounterSearcher $searcher): CounterSearchResponse
    {
        return $this->counterRepository->search($searcher);
    }

    public function getById(?int $id): ?CounterDTO
    {
        return $this->counterRepository->getById($id);
    }

    public function save(CounterDTO $counter): CounterDTO
    {
        return $this->counterRepository->save($counter);
    }

    public function deleteById(?int $id): bool
    {
        return $this->counterRepository->deleteById($id);
    }

    public function getByAccountId(?int $accountId): CounterCollection
    {
        if ( ! $accountId) {
            return new CounterCollection();
        }

        $searcher = new CounterSearcher();
        $searcher
            ->setAccountId($accountId)
            ->setSortOrderProperty(Counter::IS_INVOICING, SearcherInterface::SORT_ORDER_DESC)
            ->setSortOrderProperty(Counter::ID, SearcherInterface::SORT_ORDER_DESC)
        ;

        return $this->counterRepository->search($searcher)->getItems()->first();
    }
}
