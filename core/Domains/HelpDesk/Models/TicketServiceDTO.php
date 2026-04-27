<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Models;

use Core\Domains\Common\Traits\TimestampsTrait;

class TicketServiceDTO
{
    use TimestampsTrait;

    private ?int    $id         = null;
    private ?int    $categoryId = null;
    private ?string $name       = null;
    private ?string $code       = null;
    private ?int    $sortOrder  = null;
    private ?bool   $isActive   = null;

    private ?TicketCategoryDTO $category = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    public function setCategoryId(?int $categoryId): static
    {
        $this->categoryId = $categoryId;

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

    public function getCategory(): ?TicketCategoryDTO
    {
        return $this->category;
    }

    public function setCategory(?TicketCategoryDTO $category): static
    {
        $this->category = $category;

        return $this;
    }
}
