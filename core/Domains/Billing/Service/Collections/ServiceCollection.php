<?php declare(strict_types=1);

namespace Core\Domains\Billing\Service\Collections;

use Core\Collections\CollectionInterface;
use Core\Collections\CollectionTrait;
use Core\Domains\Billing\Claim\Models\ClaimDTO;
use Core\Domains\Billing\Service\Enums\ServiceTypeEnum;
use Core\Domains\Billing\Service\Models\ServiceDTO;
use Illuminate\Support\Collection;

/**
 * @template-extends Collection<int, ServiceDTO>
 */
class ServiceCollection extends Collection implements CollectionInterface
{
    use CollectionTrait;

    public function checkItemInstance(mixed $item): bool
    {
        return $item instanceof ServiceDTO;
    }

    /**
     * @return array<int, ServiceTypeEnum>
     */
    public function getTypes(): array
    {
        $result = [];
        foreach ($this as $service) {
            $type = $service->getType();

            $result[$type?->value] = $type;
        }

        return $result;
    }

    public function getById(int $id): ?ServiceDTO
    {
        foreach ($this as $service) {
            if ($service->getId() === $id) {
                return $service;
            }
        }

        return null;
    }

    public function getByPeriodId(?int $periodId): static
    {
        $result = new static();

        foreach ($this as $service) {
            if ($service->getPeriodId() === $periodId) {
                $result = $result->add($service);
            }
        }

        return $result;
    }

    public function sortByTypes(array $orderedTypes = [
        ServiceTypeEnum::DEBT,
        ServiceTypeEnum::MEMBERSHIP_FEE,
        ServiceTypeEnum::TARGET_FEE,
        ServiceTypeEnum::PERSONAL_FEE,
        ServiceTypeEnum::ELECTRIC_TARIFF,
        ServiceTypeEnum::OTHER,
        ServiceTypeEnum::ADVANCE_PAYMENT,
    ]): static
    {
        return $this->sort(function (ServiceDTO $a, ServiceDTO $b) use ($orderedTypes) {
            foreach ($orderedTypes as $type) {
                $compareResult = $this->orderingFunction($type, $a->getType(), $b->getType());
                if ($compareResult !== 0) {
                    return $compareResult;
                }
            }

            return 0;
        });
    }

    public function getByType(ServiceTypeEnum $type): self
    {
        return $this->filter(function (ServiceDTO $service) use ($type) {
            return $service->getType() === $type;
        });
    }
}
