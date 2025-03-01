<?php declare(strict_types=1);

namespace Core\Domains\Billing\Period\Models;

use Core\Db\Searcher\SearcherInterface;
use Core\Db\Searcher\SearcherTrait;

class PeriodSearcher implements SearcherInterface
{
    use SearcherTrait;
}
