<?php declare(strict_types=1);

namespace Core\Domains\Counter\Models;

use Core\Domains\Common\Traits\TimestampsTrait;
use Core\Domains\Counter\Enums\TypeEnum;

class CounterDTO implements \JsonSerializable
{
    use TimestampsTrait;

    private ?int      $id        = null;
    private ?TypeEnum $type      = null;
    private ?int      $accountId = null;
    private ?string   $number    = null;
    private ?bool     $isPrimary = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): CounterDTO
    {
        $this->id = $id;

        return $this;
    }

    public function getType(): ?TypeEnum
    {
        return $this->type;
    }

    public function setType(?TypeEnum $type): CounterDTO
    {
        $this->type = $type;

        return $this;
    }

    public function getAccountId(): ?int
    {
        return $this->accountId;
    }

    public function setAccountId(?int $accountId): CounterDTO
    {
        $this->accountId = $accountId;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): CounterDTO
    {
        $this->number = $number;

        return $this;
    }

    public function isPrimary(): ?bool
    {
        return $this->isPrimary;
    }

    public function setIsPrimary(?bool $isPrimary): CounterDTO
    {
        $this->isPrimary = $isPrimary;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id'        => $this->getId(),
            'type'      => $this->getType()?->value,
            'accountId' => $this->getAccountId(),
            'number'    => $this->getNumber(),
            'isPrimary' => $this->isPrimary(),
        ];
    }
}
