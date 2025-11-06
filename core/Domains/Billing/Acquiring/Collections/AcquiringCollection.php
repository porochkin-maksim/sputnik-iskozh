<?php declare(strict_types=1);

namespace Core\Domains\Billing\Acquiring\Collections;

use Core\Collections\CollectionInterface;
use Core\Collections\CollectionTrait;
use Core\Domains\Billing\Acquiring\Models\AcquiringDTO;
use Illuminate\Support\Collection;

/**
 * @template-extends Collection<int, AcquiringDTO>
 */
class AcquiringCollection extends Collection implements CollectionInterface
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof AcquiringDTO;
    }
}
