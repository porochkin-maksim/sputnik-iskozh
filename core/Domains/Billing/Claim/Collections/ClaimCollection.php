<?php declare(strict_types=1);

namespace Core\Domains\Billing\Claim\Collections;

use Core\Collections\CollectionInterface;
use Core\Collections\CollectionTrait;
use Core\Domains\Billing\Service\Enums\ServiceTypeEnum;
use Core\Domains\Billing\Claim\Models\ClaimDTO;
use Illuminate\Support\Collection;

/**
 * @template-extends Collection<int, ClaimDTO>
 */
class ClaimCollection extends Collection implements CollectionInterface
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof ClaimDTO;
    }

    /**
     * @param array<int, ServiceTypeEnum> $orderedTypes
     */
    public function sortByServiceTypes(array $orderedTypes): static
    {
        return $this->sort(function (ClaimDTO $claim1, ClaimDTO $claim2) use ($orderedTypes) {
            foreach ($orderedTypes as $type) {
                $compareResult = $this->orderingFunction($type, $claim1->getService()?->getType(), $claim2->getService()?->getType());
                if ($compareResult !== 0) {
                    return $compareResult;
                }
            }

            return 0;
        });
    }
}
