<?php declare(strict_types=1);

namespace Core\Domains\Billing\Invoice;

use Core\Shared\Collections\Collection;
use Core\Shared\Collections\CollectionTrait;

/**
 * @template-extends Collection<int, InvoiceEntity>
 */
class InvoiceCollection extends Collection
{
    use CollectionTrait;
}
