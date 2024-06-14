<?php declare(strict_types=1);

namespace Core\Domains\Report\Models;

use Core\Db\Searcher\SearcherInterface;
use Core\Db\Searcher\SearcherTrait;

class ReportSearcher implements SearcherInterface
{
    use SearcherTrait;

    public function setWithFiles(): static
    {
        $this->with[] = 'files';

        return $this;
    }
}
