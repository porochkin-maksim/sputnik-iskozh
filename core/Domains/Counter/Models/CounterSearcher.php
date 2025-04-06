<?php declare(strict_types=1);

namespace Core\Domains\Counter\Models;

use App\Models\Counter\Counter;
use Core\Db\Searcher\SearcherInterface;
use Core\Db\Searcher\SearcherTrait;
use Core\Domains\Counter\Enums\CounterTypeEnum;

class CounterSearcher implements SearcherInterface
{
    use SearcherTrait;

    public function __construct()
    {
        $this->setType(CounterTypeEnum::ELECTRICITY);
    }

    public function setWithHistory(): static
    {
        $this->with[] = Counter::HISTORY;

        return $this;
    }

    public function setType(CounterTypeEnum $enum): static
    {
        $this->addWhere(Counter::TYPE, SearcherInterface::EQUALS, $enum->value);

        return $this;
    }

    public function setAccountId(?int $id): static
    {
        $this->addWhere(Counter::ACCOUNT_ID, SearcherInterface::EQUALS, $id);

        return $this;
    }

    public function setHasIncrement(): static
    {
        $this->addWhere(Counter::INCREMENT, SearcherInterface::IS_NOT_NULL);

        return $this;
    }
}
