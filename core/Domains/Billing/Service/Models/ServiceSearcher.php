<?php declare(strict_types=1);

namespace Core\Domains\Billing\Service\Models;

use App\Models\Billing\Service;
use Core\Db\Searcher\SearcherInterface;
use Core\Db\Searcher\SearcherTrait;
use Core\Domains\Billing\Service\Enums\ServiceTypeEnum;

class ServiceSearcher implements SearcherInterface
{
    use SearcherTrait;

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

    public function exludeType(ServiceTypeEnum $type): static
    {
        $this->addWhere(Service::TYPE, SearcherInterface::IS_NOT, $type->value);

        return $this;
    }

    public function withPeriods(): static
    {
        $this->with[] = Service::PERIOD;

        return $this;
    }
}
