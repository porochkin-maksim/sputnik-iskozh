<?php declare(strict_types=1);

namespace Core\Domains\Billing\Transaction\Collections;

use Core\Collections\CollectionInterface;
use Core\Collections\CollectionTrait;
use Core\Domains\Billing\Service\Enums\ServiceTypeEnum;
use Core\Domains\Billing\Transaction\Models\TransactionDTO;
use Illuminate\Support\Collection;

/**
 * @template-extends Collection<int, TransactionDTO>
 */
class TransactionCollection extends Collection implements CollectionInterface
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof TransactionDTO;
    }

    /**
     * @param array<int, ServiceTypeEnum> $orderedTypes
     */
    public function sortByServiceTypes(array $orderedTypes): static
    {
        return $this->sort(function (TransactionDTO $transaction1, TransactionDTO $transaction2) use ($orderedTypes) {
            foreach ($orderedTypes as $type) {
                $compareResult = $this->orderingFunction($type, $transaction1->getService()?->getType(), $transaction2->getService()?->getType());
                if ($compareResult !== 0) {
                    return $compareResult;
                }
            }

            return 0;
        });
    }
}
