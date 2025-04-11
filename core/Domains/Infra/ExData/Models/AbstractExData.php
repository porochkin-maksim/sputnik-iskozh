<?php declare(strict_types=1);

namespace Core\Domains\Infra\ExData\Models;

use Core\Domains\Infra\ExData\Enums\ExDataTypeEnum;
use Core\Domains\Common\Traits\TimestampsTrait;
use JsonSerializable;

abstract class AbstractExData implements JsonSerializable
{
    use TimestampsTrait;

    private ?int $id = null;

    abstract public function getType(): ExDataTypeEnum;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }
} 