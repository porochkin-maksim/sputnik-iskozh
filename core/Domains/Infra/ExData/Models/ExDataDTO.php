<?php declare(strict_types=1);

namespace Core\Domains\Infra\ExData\Models;

use Core\Domains\Infra\ExData\Enums\ExDataTypeEnum;
use Core\Domains\Common\Traits\TimestampsTrait;

class ExDataDTO
{
    use TimestampsTrait;

    private ?int            $id          = null;
    private ?ExDataTypeEnum $type        = null;
    private ?int            $referenceId = null;
    private ?array          $data        = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getType(): ?ExDataTypeEnum
    {
        return $this->type;
    }

    public function setType(?ExDataTypeEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getReferenceId(): ?int
    {
        return $this->referenceId;
    }

    public function setReferenceId(?int $referenceId): static
    {
        $this->referenceId = $referenceId;

        return $this;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function setData(?array $data): static
    {
        $this->data = $data;

        return $this;
    }
} 