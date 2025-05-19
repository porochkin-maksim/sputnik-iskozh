<?php declare(strict_types=1);

namespace Core\Domains\Billing\Period\Collections;

use Core\Collections\CollectionInterface;
use Core\Collections\CollectionTrait;
use Core\Domains\Billing\Period\Models\PeriodDTO;
use Illuminate\Support\Collection;

/**
 * @template-extends Collection<int, PeriodDTO>
 */
class PeriodCollection extends Collection implements CollectionInterface
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof PeriodDTO;
    }

    public function getCurrent(): ?PeriodDTO
    {
        foreach ($this as $period) {
            if ($period->isCurrent()) {
                return $period;
            }
        }

        return null;
    }

    public function getActive(): static
    {
        return $this->filter(function (PeriodDTO $period) {
            return ! $period->isClosed();
        });
    }
}
