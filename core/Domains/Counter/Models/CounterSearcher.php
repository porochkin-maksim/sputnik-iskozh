<?php declare(strict_types=1);

namespace Core\Domains\Counter\Models;

use App\Models\Counter;
use Core\Db\Searcher\SearcherInterface;
use Core\Db\Searcher\SearcherTrait;
use Core\Domains\Counter\Enums\TypeEnum;

class CounterSearcher implements SearcherInterface
{
    use SearcherTrait;

    public function __construct()
    {
        $this->setType(TypeEnum::ELECTRICITY);
    }

    public function setType(TypeEnum $enum): static
    {
        $this->addWhere(Counter::TYPE, SearcherInterface::EQUALS, $enum->value);

        return $this;
    }

    public function setAccountId(?int $id): static
    {
        $this->addWhere(Counter::ACCOUNT_ID, SearcherInterface::EQUALS, $id);

        return $this;
    }
}
