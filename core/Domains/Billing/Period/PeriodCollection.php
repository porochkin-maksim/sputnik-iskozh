<?php declare(strict_types=1);

namespace Core\Domains\Billing\Period;

use Core\Shared\Collections\Collection;
use Core\Shared\Collections\CollectionTrait;

/**
 * @template-extends Collection<int, PeriodEntity>
 */
class PeriodCollection extends Collection
{
    use CollectionTrait;

    public function getCurrent(): ?PeriodEntity
    {
        return $this->find(function (PeriodEntity $period) {
            return $period->isCurrent();
        });
    }

    public function getActive(): static
    {
        return $this->filter(function (PeriodEntity $period) {
            return ! $period->isClosed();
        });
    }
}
