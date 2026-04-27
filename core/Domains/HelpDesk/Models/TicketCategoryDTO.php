<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Models;

use Core\Domains\Common\Traits\TimestampsTrait;
use Core\Domains\HelpDesk\Collection\TicketServiceCollection;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\HelpDesk\HelpDeskServiceLocator;

class TicketCategoryDTO
{
    use TimestampsTrait;

    private ?int            $id        = null;
    private ?TicketTypeEnum $type      = null;
    private ?string         $name      = null;
    private ?string         $code      = null;
    private ?int            $sortOrder = null;
    private ?bool           $isActive  = null;

    private ?TicketServiceCollection $services = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getType(): ?TicketTypeEnum
    {
        return $this->type;
    }

    public function setType(?TicketTypeEnum $type): static
    {
        $this->type = $type;

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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getSortOrder(): ?int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(?int $sortOrder): static
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getServices(bool $lazyLoad = false): ?TicketServiceCollection
    {
        if ( ! $this->services && $lazyLoad) {
            $this->services = HelpDeskServiceLocator::TicketServiceService()->getByCategoryId($this->getId());
        }
        return $this->services;
    }

    public function setServices(?TicketServiceCollection $services): static
    {
        $this->services = $services;

        return $this;
    }
}
