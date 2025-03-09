<?php declare(strict_types=1);

namespace Core\Domains\Billing\Invoice\Collections;

use Core\Collections\CollectionInterface;
use Core\Collections\CollectionTrait;
use Core\Domains\Billing\Invoice\Models\InvoiceDTO;
use Illuminate\Support\Collection;

/**
 * @template-extends Collection<int, InvoiceDTO>
 */
class InvoiceCollection extends Collection implements CollectionInterface
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof InvoiceDTO;
    }
}
