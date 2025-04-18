<?php declare(strict_types=1);

namespace Core\Domains\Counter\Models;

use Carbon\Carbon;
use Core\Domains\Billing\Claim\Models\ClaimDTO;
use Core\Domains\Common\Traits\TimestampsTrait;
use Core\Domains\File\Models\FileDTO;

class CounterHistoryDTO
{
    use TimestampsTrait;

    private ?int    $id          = null;
    private ?int    $counter_id  = null;
    private ?int    $previous_id = null;
    private ?float  $value       = null;
    private ?Carbon $date        = null;
    private ?bool   $is_verified = null;

    private ?float $previous_value = null;

    private ?FileDTO           $file     = null;
    private ?CounterDTO        $counter  = null;
    private ?ClaimDTO          $claimDTO = null;
    private ?CounterHistoryDTO $previous = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getCreatedAt(): ?Carbon
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?Carbon $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?Carbon
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?Carbon $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCounterId(): ?int
    {
        return $this->counter_id;
    }

    public function setCounterId(?int $counter_id): static
    {
        $this->counter_id = $counter_id;

        return $this;
    }

    public function getPreviousId(): ?int
    {
        return $this->previous_id;
    }

    public function setPreviousId(?int $previous_id): static
    {
        $this->previous_id = $previous_id;

        return $this;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(?float $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getDate(): ?Carbon
    {
        return $this->date;
    }

    public function setDate(?Carbon $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function isVerified(): ?bool
    {
        return $this->is_verified;
    }

    public function setIsVerified(?bool $is_verified): static
    {
        $this->is_verified = $is_verified;

        return $this;
    }

    public function setPreviousValue(?float $value): static
    {
        $this->previous_value = $value;

        return $this;
    }

    public function getPreviousValue(): ?float
    {
        return $this->previous_value;
    }

    public function setFile(FileDTO $file): static
    {
        $this->file = $file;

        return $this;
    }

    public function getFile(): ?FileDTO
    {
        return $this->file;
    }

    public function setCounter(?CounterDTO $counter): static
    {
        $this->counter = $counter;

        return $this;
    }

    public function getCounter(): ?CounterDTO
    {
        return $this->counter;
    }

    public function getClaim(): ?ClaimDTO
    {
        return $this->claimDTO;
    }

    public function setClaim(?ClaimDTO $claimDTO): static
    {
        $this->claimDTO = $claimDTO;

        return $this;
    }

    public function setPrevious(?CounterHistoryDTO $previous): static
    {
        $this->previous = $previous;

        return $this;
    }

    public function getPrevious(): ?CounterHistoryDTO
    {
        return $this->previous;
    }

    public function getDelta(): ?float
    {
        return $this->getPreviousValue() ? ($this->getValue() - $this->getPreviousValue()) : null;
    }
}
