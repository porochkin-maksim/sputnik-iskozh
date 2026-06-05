<?php declare(strict_types=1);

namespace Core\Domains\Billing\Service;

use App\Models\Billing\Service;
use Core\Repositories\BaseSearcher;
use Core\Repositories\SearcherInterface;

class ServiceSearcher extends BaseSearcher
{
    public function setActive(bool $active): static
    {
        $this->addWhere(Service::ACTIVE, SearcherInterface::EQUALS, $active);

        return $this;
    }

    public function setPeriodId(?int $periodId): static
    {
        $this->addWhere(Service::PERIOD_ID, SearcherInterface::EQUALS, $periodId);

        return $this;
    }

    public function setType(ServiceTypeEnum $type): static
    {
        $this->addWhere(Service::TYPE, SearcherInterface::EQUALS, $type->value);

        return $this;
    }

    public function excludeType(ServiceTypeEnum $type): static
    {
        $this->addWhere(Service::TYPE, SearcherInterface::IS_NOT, $type->value);

        return $this;
    }

    public function withPeriods(): static
    {
        $this->with[] = Service::RELATION_PERIOD;

        return $this;
    }
}
