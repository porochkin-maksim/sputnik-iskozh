<?php declare(strict_types=1);

namespace Core\Objects\Access\Models;

use Core\Db\Searcher\SearcherInterface;
use Core\Db\Searcher\SearcherTrait;

class RolesSearcher implements SearcherInterface
{
    use SearcherTrait;
}
