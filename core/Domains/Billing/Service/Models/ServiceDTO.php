<?php declare(strict_types=1);

namespace Core\Domains\Billing\Service\Models;

use Core\Domains\Billing\Service\Enums\ServiceTypeEnum;
use Core\Domains\Common\Traits\TimestampsTrait;

class ServiceDTO
{
    use TimestampsTrait;

    private ?int             $id        = null;
    private ?ServiceTypeEnum $type      = null;
    private ?int             $period_id = null;
    private ?string          $name      = null;
    private ?float           $cost      = null;
    private ?bool            $active    = null;

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
        return $this->period_id;
    }

    public function setPeriodId(?int $period_id): static
    {
        $this->period_id = $period_id;

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

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setIsActive(?bool $active): static
    {
        $this->active = $active;

        return $this;
    }
}
