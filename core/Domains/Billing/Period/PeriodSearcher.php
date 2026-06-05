<?php declare(strict_types=1);

namespace Core\Domains\Billing\Period;

use App\Models\Billing\Period;
use Core\Repositories\BaseSearcher;
use Core\Repositories\SearcherInterface;

class PeriodSearcher extends BaseSearcher
{
    public function setIsClosed(bool $isClosed): static
    {
        $this->addWhere(Period::IS_CLOSED, SearcherInterface::EQUALS, $isClosed);

        return $this;
    }
}
