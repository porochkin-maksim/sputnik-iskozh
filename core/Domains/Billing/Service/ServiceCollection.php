<?php declare(strict_types=1);

namespace Core\Domains\Billing\Service;

use Core\Shared\Collections\CollectionTrait;
use Core\Shared\Collections\Collection;;

/**
 * @template-extends Collection<int, ServiceEntity>
 */
class ServiceCollection extends Collection
{
    use CollectionTrait;

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

    public function getById(int $id): ?ServiceEntity
    {
        return $this->first(function (ServiceEntity $service) use ($id) {
            return $service->getId() === $id;
        });
    }

    public function getByPeriodId(?int $periodId): static
    {
        return $this->filter(function (ServiceEntity $service) use ($periodId) {
            return $service->getPeriodId() === $periodId;
        });
    }

    public function sortByTypes(array $orderedTypes = [
        ServiceTypeEnum::DEBT,
        ServiceTypeEnum::MEMBERSHIP_FEE,
        ServiceTypeEnum::TARGET_FEE,
        ServiceTypeEnum::PERSONAL_FEE,
        ServiceTypeEnum::ELECTRIC_TARIFF,
        ServiceTypeEnum::OTHER,
        ServiceTypeEnum::ADVANCE_PAYMENT,
    ]): static {
        return $this->sort(function (ServiceEntity $a, ServiceEntity $b) use ($orderedTypes) {
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
        return $this->filter(function (ServiceEntity $service) use ($type) {
            return $service->getType() === $type;
        });
    }
}
