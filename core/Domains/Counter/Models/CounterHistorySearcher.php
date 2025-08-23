<?php declare(strict_types=1);

namespace Core\Domains\Counter\Models;

use App\Models\Counter\CounterHistory;
use Core\Db\Searcher\SearcherInterface;
use Core\Db\Searcher\SearcherTrait;

class CounterHistorySearcher implements SearcherInterface
{
    use SearcherTrait;

    public function setVerified(bool $verified): static
    {
        $this->addWhere(CounterHistory::IS_VERIFIED, SearcherInterface::EQUALS, $verified);

        return $this;
    }

    public function setWithCounter(): static
    {
        $this->with[] = CounterHistory::COUNTER;

        return $this;
    }

    public function setWithPrevious(): static
    {
        $this->with[] = CounterHistory::PREVIOUS;

        return $this;
    }

    public function defaultSort(): static
    {
        return $this
            ->setSortOrderProperty(CounterHistory::COUNTER_ID, SearcherInterface::SORT_ORDER_DESC)
            ->setSortOrderProperty(CounterHistory::DATE, SearcherInterface::SORT_ORDER_ASC)
            ->setSortOrderProperty(CounterHistory::CREATED_AT, SearcherInterface::SORT_ORDER_ASC)
            ->setSortOrderProperty(CounterHistory::ID, SearcherInterface::SORT_ORDER_ASC)
        ;
    }

    public function descSort(): static
    {
        return $this
            ->setSortOrderProperty(CounterHistory::COUNTER_ID, SearcherInterface::SORT_ORDER_DESC)
            ->setSortOrderProperty(CounterHistory::DATE, SearcherInterface::SORT_ORDER_DESC)
            ->setSortOrderProperty(CounterHistory::ID, SearcherInterface::SORT_ORDER_DESC)
        ;
    }

    public function setCounterId(?int $getCounterId): static
    {
        if (null !== $getCounterId) {
            $this->addWhere(CounterHistory::COUNTER_ID, SearcherInterface::EQUALS, $getCounterId);
        }

        return $this;
    }

    public function setCounterIds(array $counterIds): static
    {
        $this->addWhere(CounterHistory::COUNTER_ID, SearcherInterface::IN, $counterIds);

        return $this;
    }

    public function setPreviousId(int $counterHistoryId): static
    {
        $this->addWhere(CounterHistory::PREVIOUS_ID, SearcherInterface::EQUALS, $counterHistoryId);

        return $this;
    }

    public function setWithClaim(): static
    {
        $this->with[] = CounterHistory::CLAIM;

        return $this;
    }
}
