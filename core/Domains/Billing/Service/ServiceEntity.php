<?php declare(strict_types=1);

namespace Core\Domains\Billing\Service;

use Carbon\Carbon;
use Core\Domains\Billing\Period\PeriodEntity;
use Core\Domains\Common\Traits\TimestampsTrait;
use Core\Shared\Helpers\DateTime\DateTimeHelper;

class ServiceEntity
{
    use TimestampsTrait;

    private ?int             $id         = null;
    private ?ServiceTypeEnum $type       = null;
    private ?int             $periodId   = null;
    private ?string          $name       = null;
    private ?float           $cost       = null;
    private ?Carbon          $periodFrom = null;
    private ?Carbon          $periodTo   = null;
    private ?PeriodEntity    $period     = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getType(): ?ServiceTypeEnum
    {
        return $this->type;
    }

    public function setType(?ServiceTypeEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getPeriodId(): ?int
    {
        return $this->periodId;
    }

    public function setPeriodId(?int $periodId): static
    {
        $this->periodId = $periodId;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCost(): ?float
    {
        return $this->cost;
    }

    public function setCost(?float $cost): static
    {
        $this->cost = $cost;

        return $this;
    }

    public function getPeriodFrom(): ?Carbon
    {
        return $this->periodFrom;
    }

    public function setPeriodFrom(mixed $periodFrom): static
    {
        $this->periodFrom = DateTimeHelper::toCarbonOrNull($periodFrom);

        return $this;
    }

    public function getPeriodTo(): ?Carbon
    {
        return $this->periodTo;
    }

    public function setPeriodTo(mixed $periodTo): static
    {
        $this->periodTo = DateTimeHelper::toCarbonOrNull($periodTo);

        return $this;
    }

    public function getPeriod(): ?PeriodEntity
    {
        return $this->period;
    }

    public function setPeriod(?PeriodEntity $period): static
    {
        $this->period = $period;

        return $this;
    }
}
