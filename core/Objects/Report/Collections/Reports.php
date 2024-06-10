<?php declare(strict_types=1);

namespace Core\Objects\Report\Collections;

use Core\Collections\CollectionInterface;
use Core\Collections\CollectionTrait;
use Core\Objects\Report\Models\ReportDTO;
use Illuminate\Support\Collection;

/**
 * @template-extends Collection<int, ReportDTO>
 */
class Reports extends Collection implements CollectionInterface
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof ReportDTO;
    }
}
