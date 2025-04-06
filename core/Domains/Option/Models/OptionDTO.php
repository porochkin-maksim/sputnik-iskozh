<?php declare(strict_types=1);

namespace Core\Domains\Option\Models;

use Core\Domains\Common\Traits\TimestampsTrait;
use Core\Domains\Option\Enums\OptionEnum;
use Core\Domains\Option\Models\DataDTO\DataDTOInterface;
use JsonSerializable;

class OptionDTO implements JsonSerializable
{
    use TimestampsTrait;

    private ?int              $id   = null;
    private ?OptionEnum       $type = null;
    private ?DataDTOInterface $data = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getType(): ?OptionEnum
    {
        return $this->type;
    }

    public function setType(?OptionEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getData(): ?DataDTOInterface
    {
        return $this->data;
    }

    public function setData(?DataDTOInterface $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id'   => $this->getId(),
            'name' => $this->getType()?->name(),
            'data' => $this->getData(),
        ];
    }
}
