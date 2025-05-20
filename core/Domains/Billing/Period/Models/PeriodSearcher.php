<?php declare(strict_types=1);

namespace Core\Domains\Billing\Period\Models;

use App\Models\Billing\Period;
use Core\Db\Searcher\SearcherInterface;
use Core\Db\Searcher\SearcherTrait;

class PeriodSearcher implements SearcherInterface
{
    use SearcherTrait;

    public function setIsClosed(bool $arg): static
    {
        $this->addWhere(Period::IS_CLOSED, SearcherInterface::EQUALS, $arg);

        return $this;
    }
}
